<?php

declare(strict_types=1);

namespace App\Controller\Discogs;

use App\Controller\AbstractApplicationController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use UnexpectedValueException;

use function file_get_contents;
use function sprintf;

#[Route('/api/discogs')]
final class ApiController extends AbstractApplicationController
{
    #[Route('/release/{releaseId<\d+>}', name: 'api_discogs_release', methods: ['GET'])]
    public function getRelease(int $releaseId): Response
    {
        return new Response(
            $this->createResponseData(),
            200,
            [
                'Content-Type' => 'application/json',
                'x-discogs-ratelimit' => 60,
            ],
        );
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
