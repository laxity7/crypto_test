<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CurrencyRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrencyRate>
 */
class CurrencyRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRate::class);
    }

    /**
     * @param non-empty-string $pair
     *
     * @return CurrencyRate[]
     */
    public function findLast24HoursByPair(string $pair): array
    {
        $since = new \DateTimeImmutable('-24 hours');

        /** @var CurrencyRate[] $result */
        $result = $this->createQueryBuilder('cr')
                       ->andWhere('cr.pair = :pair')
                       ->andWhere('cr.createdAt >= :since')
                       ->setParameter('pair', $pair)
                       ->setParameter('since', $since)
                       ->orderBy('cr.createdAt', 'DESC')
                       ->getQuery()
                       ->getResult();

        return $result;
    }

    /**
     * @param non-empty-string $pair
     *
     * @return CurrencyRate[]
     */
    public function findByDayAndPair(string $pair, \DateTimeImmutable $date): array
    {
        $startOfDay = $date->setTime(0, 0, 0);
        $endOfDay = $date->setTime(23, 59, 59);

        /** @var CurrencyRate[] $result */
        $result = $this->createQueryBuilder('cr')
                       ->andWhere('cr.pair = :pair')
                       ->andWhere('cr.createdAt BETWEEN :start AND :end')
                       ->setParameter('pair', $pair)
                       ->setParameter('start', $startOfDay)
                       ->setParameter('end', $endOfDay)
                       ->orderBy('cr.createdAt', 'DESC')
                       ->getQuery()
                       ->getResult();

        return $result;
    }
}
