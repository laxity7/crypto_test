<?php

declare(strict_types=1);

namespace App\Task;

use App\Entity\CurrencyRate;
use App\Service\ExchangeProviderInterface;
use App\Service\ExchangeService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class FetchRatesTask
{
    public function __construct(
        private readonly ExchangeService $exchangeService,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(ExchangeProviderInterface $exchangeProvider): void
    {
        $this->logger->info('CronTask triggered: Fetching currency rates from {provider}.', [
            'provider' => $this->exchangeService->getProviderName($exchangeProvider),
        ]);
        try {
            $priceObjects = $this->exchangeService->fetchPrices($exchangeProvider);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to get prices from exchange service: {message}', ['message' => $e->getMessage()]);

            return;
        }

        foreach ($priceObjects as $priceDto) {
            $currencyRate = new CurrencyRate($priceDto->pair, $priceDto->price);
            $this->entityManager->persist($currencyRate);
        }

        $this->entityManager->flush();
        $this->logger->info('Successfully stored {count} currency rates.', ['count' => count($priceObjects)]);
    }

    public function __invoke(ExchangeProviderInterface $exchangeProvider): void
    {
        $this->handle($exchangeProvider);
    }
}
