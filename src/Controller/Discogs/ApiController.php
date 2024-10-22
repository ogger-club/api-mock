<?php

declare(strict_types=1);

namespace App\Controller\Discogs;

use App\Controller\AbstractApplicationController;
use App\Factory\Log\LoggerFactoryFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use UnexpectedValueException;

use function file_get_contents;
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
    private const int RATE_LIMIT_TOTAL = 60;

    public function __construct(private LoggerFactoryFactory $loggerFactoryFactory)
    {
    }

    #[Route('/releases/{releaseId<\d+>}', name: 'api_discogs_release', methods: ['GET'])]
    public function getRelease(int $releaseId): Response
    {
        $logger = $this->createLogger('api_discogs_release');
        $logger->debug(__FUNCTION__);
        $logger->debug(sprintf('Release id: %d', $releaseId));

        return new Response(
            $this->createResponseData(),
            200,
            [
                'Content-Type' => 'application/json',
                'x-discogs-ratelimit' => self::RATE_LIMIT_TOTAL,
            ],
        );
    }

    private function createLogger(string $channel): LoggerInterface
    {
        $loggerFactory = $this->loggerFactoryFactory->createLoggerFactory($this->createProjectPathFromParameter());

        return $loggerFactory->createLogger($channel);
    }

    private function createResponseData(): string
    {
        $projectPath = $this->createProjectPathFromParameter();

        $data = file_get_contents(sprintf('%s/resources/api/discogs/releases/1.json', $projectPath));

        if ($data === false) {
            throw new UnexpectedValueException('Error converting array to json.');
        }

        return $data;
    }
}
