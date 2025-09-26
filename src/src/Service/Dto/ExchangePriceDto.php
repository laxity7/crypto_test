<?php

declare(strict_types=1);

namespace App\Service\Dto;

readonly class ExchangePriceDto
{
    public function __construct(
        public string $pair,
        public string $price,
        public \DateTimeImmutable $timestamp
    ) {
    }
}
