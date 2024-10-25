<?php

declare(strict_types=1);

namespace App\Controller\Discogs;

use App\Controller\AbstractApplicationController;
use App\Factory\Log\LoggerFactoryFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use UnexpectedValueException;

use function file_get_contents;
use function json_encode;
use function sprintf;

/**
 * Psalm: Symfony related error:
 * "Property AppController::$container is not defined in constructor of AppController
 *  or in any methods called in the constructor (see https://psalm.dev/074)"
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @todo check https://github.com/psalm/psalm-plugin-symfony
 */
#[Route('/api/discogs')]
final class ApiController extends AbstractApplicationController
{
    public function __construct(
        private LoggerFactoryFactory $loggerFactoryFactory,
        private RateLimiterFactory $discogsApiLimiter,
    ) {
    }

    #[Route('/releases/{releaseId<\d+>}', name: 'api_discogs_release', methods: ['GET'])]
    public function getRelease(int $releaseId, Request $request): Response
    {
        $clientIp = $this->getClientIp($request);

        $limiter = $this->discogsApiLimiter->create($clientIp);
        $rateLimit = $limiter->consume(1);

        $headers = $this->createHeaders($rateLimit);

        $responseStatusCode = $this->getResponseStatusCode($rateLimit);

        $this->log(
            $clientIp,
            $headers,
            $releaseId,
            $responseStatusCode,
            $request->attributes->getString('_route'),
        );

        return new Response(
            $this->createResponseData($responseStatusCode),
            $responseStatusCode,
            $headers,
        );
    }

    /**
     * @return array<string,int|string>
     */
    private function createHeaders(RateLimit $rateLimit): array
    {
        $rateLimitTotal = $rateLimit->getLimit();
        $rateLimitRemaining = $rateLimit->getRemainingTokens();

        return [
            'content-type' => 'application/json',
            'x-discogs-ratelimit' => $rateLimitTotal,
            'x-discogs-ratelimit-remaining' => $rateLimitRemaining,
            'x-discogs-ratelimit-used' => $rateLimitTotal - $rateLimitRemaining,
        ];
    }

    private function createLogger(string $channel): LoggerInterface
    {
        $loggerFactory = $this->loggerFactoryFactory->createLoggerFactory($this->createProjectPathFromParameter());

        return $loggerFactory->createLogger($channel);
    }

    private function createResponseData(int $responseStatusCode): ?string
    {
        if ($responseStatusCode !== Response::HTTP_OK) {
            return null;
        }

        $projectPath = $this->createProjectPathFromParameter();

        $data = file_get_contents(sprintf('%s/resources/api/discogs/releases/1.json', $projectPath));

        if ($data === false) {
            throw new UnexpectedValueException('Error converting array to json.');
        }

        return $data;
    }

    private function getResponseStatusCode(RateLimit $rateLimit): int
    {
        $responseStatusCode = Response::HTTP_OK;

        // Somehow this does not work, @todo check why.
        //$isAccepted = $rateLimit->isAccepted();
        $isAccepted = $rateLimit->getRemainingTokens() > 0;
        if ($isAccepted === false) {
            $responseStatusCode = Response::HTTP_TOO_MANY_REQUESTS;
        }

        return $responseStatusCode;
    }

    /**
     * @param array<string,int|string> $headers
     */
    private function log(
        string $clientIp,
        array $headers,
        int $releaseId,
        int $responseStatusCode,
        string $route,
    ): bool {
        $logger = $this->createLogger('api_discogs_release');

        $logger->debug(
            sprintf(
                '%s %s %d %d %s',
                $clientIp,
                $route,
                $releaseId,
                $responseStatusCode,
                json_encode($headers),
            ),
        );

        return true;
    }
}
