<?php

declare(strict_types=1);

namespace App\Controller\Discogs;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use UnexpectedValueException;

use function json_encode;

#[Route('/api/discogs')]
final class ApiController extends AbstractController
{
    #[Route('/release/{releaseId<\d+>}', name: 'api_discogs_release', methods: ['GET'])]
    public function getRelease(int $releaseId): Response
    {
        return new Response(
            $this->createResponseData($releaseId),
            200,
            [
                'Content-Type' => 'application/json',

            ],
        );
    }

    private function createResponseData(int $releaseId): string
    {
        $data = json_encode(['release' => $releaseId]);

        if ($data === false) {
            throw new UnexpectedValueException('Error converting array to json.');
        }

        return $data;
    }
}
