<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\Response\RateResponseDto;
use App\Repository\CurrencyRateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/rates')]
class RateController extends AbstractController
{
    /**
     * @param string[] $allowedPairs
     */
    public function __construct(
        private readonly CurrencyRateRepository $repository,
        private readonly ValidatorInterface $validator,
        private readonly array $allowedPairs
    ) {
    }

    #[Route('/last-24h')]
    public function last24h(Request $request): Response
    {
        $pairParam = $request->query->get('pair');
        $pair = $this->validateAndMapPair($pairParam);

        return $this->json(['data' => RateResponseDto::fromEntities(...$this->repository->findLast24HoursByPair($pair))]);
    }

    #[Route('/day')]
    public function byDay(Request $request): Response
    {
        $pairParam = $request->query->get('pair');
        $pair = $this->validateAndMapPair($pairParam);
        $dateParam = $request->query->get('date');
        $date = $this->validateDateAndCast($dateParam);

        return $this->json(['data' => RateResponseDto::fromEntities(...$this->repository->findByDayAndPair($pair, $date))]);
    }

    /**
     * @return non-empty-string
     */
    private function validateAndMapPair(mixed $pair): string
    {
        if (!is_string($pair) || trim($pair) === '') {
            throw new BadRequestHttpException('Query parameter "pair" is required.');
        }

        $parts = explode('/', $pair);
        if (count($parts) !== 2) {
            throw new BadRequestHttpException(sprintf('Invalid pair format "%s". Expected format: EUR/BTC', $pair));
        }

        $pairFormatted = $parts[1] . $parts[0];
        if (!in_array($pairFormatted, $this->allowedPairs, true)) {
            throw new BadRequestHttpException(sprintf('Invalid pair "%s".', $pair));
        }

        /** @var non-empty-string $pairFormatted */
        return $pairFormatted;
    }

    private function validateDateAndCast(mixed $date): \DateTimeImmutable
    {
        if (!is_string($date)) {
            throw new BadRequestHttpException('Query parameter "date" must be a string.');
        }

        if (count($this->validator->validate($date, [new Assert\NotBlank(), new Assert\Date()])) > 0) {
            throw new BadRequestHttpException('Invalid date format.');
        }

        return new \DateTimeImmutable($date);
    }
}
