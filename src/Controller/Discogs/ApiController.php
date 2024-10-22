<?php

declare(strict_types=1);

namespace App\Controller\Discogs;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/discogs')]
final class ApiController extends AbstractController
{
    #[Route('/release/{releaseId<\d+>}', name: 'api_discogs_release', methods: ['GET'])]
    public function getRelease(int $releaseId): Response
    {
        return $this->json(['release' => $releaseId]);
    }
}
