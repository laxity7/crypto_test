<?php

declare(strict_types=1);

namespace App\Controller\Api\Response;

use App\Entity\CurrencyRate;

final readonly class RateResponseDto
{
    public function __construct(public string $price, public string $timestamp)
    {
    }

    public static function fromEntity(CurrencyRate $rate): self
    {
        $formattedPrice = number_format((float)$rate->getPrice(), 2, '.', '');

        return new self($formattedPrice, $rate->getCreatedAt()->format('Y-m-d H:i:s'));
    }

    /** @return self[] */
    public static function fromEntities(CurrencyRate ...$rates): array
    {
        return array_map(static fn (CurrencyRate $rate): self => self::fromEntity($rate), $rates);
    }
}
