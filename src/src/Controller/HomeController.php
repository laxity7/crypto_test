<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message'   => 'Welcome to Crypto API',
            'status'    => 'OK',
            'version'   => '1.0.0',
            'timestamp' => new \DateTimeImmutable()->format('c'),
            'endpoints' => [
                'rates_last_24h' => '/api/rates/last-24h?pair=EUR/BTC',
                'rates_by_day'   => '/api/rates/day?pair=EUR/BTC&date=2023-01-01',
            ],
        ]);
    }
}
