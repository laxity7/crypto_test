<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Dto\ExchangePriceDto;

interface ExchangeProviderInterface
{
    /**
     * @return ExchangePriceDto[]
     */
    public function getPrices(): array;

    /**
     * @return non-empty-string[] For example: ['BTCEUR', 'ETHEUR']
     */
    public function getSupportedPairs(): array;
}
