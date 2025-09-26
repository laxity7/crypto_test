<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Dto\ExchangePriceDto;
use Psr\Log\LoggerInterface;

readonly class ExchangeService
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * @return ExchangePriceDto[]
     */
    public function fetchPrices(ExchangeProviderInterface $exchangeProvider): array
    {
        try {
            return $exchangeProvider->getPrices();
        } catch (\Throwable $e) {
            $this->logger->error('Failed to fetch prices from exchange provider: {message}', [
                'message'  => $e->getMessage(),
                'provider' => get_class($exchangeProvider),
            ]);
            throw $e;
        }
    }

    /**
     * @return non-empty-string[]
     */
    public function getSupportedPairs(ExchangeProviderInterface $exchangeProvider): array
    {
        return $exchangeProvider->getSupportedPairs();
    }

    public function getProviderName(ExchangeProviderInterface $exchangeProvider): string
    {
        return get_class($exchangeProvider);
    }
}
