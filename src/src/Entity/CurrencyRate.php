<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CurrencyRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRateRepository::class)]
#[ORM\Index(columns: ['pair', 'created_at'], name: 'pair_created_at_idx')]
class CurrencyRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // @phpstan-ignore property.unusedType
    private ?int $id = null;
    #[ORM\Column(length: 10)]
    private string $pair;
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 8)]
    private string $price;
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $pair, string $price)
    {
        $this->pair = $pair;
        $this->price = $price;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPair(): string
    {
        return $this->pair;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
