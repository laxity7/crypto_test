<?php

declare(strict_types=1);

namespace App\Service\Provider;

use App\Service\Dto\ExchangePriceDto;
use App\Service\ExchangeProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BinanceProvider implements ExchangeProviderInterface
{
    /**
     * @param list<non-empty-string> $allowedPairs
     */
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger,
        private readonly string $binanceApiBaseUrl,
        private readonly array $allowedPairs,
    ) {
    }

    public function getPrices(): array
    {
        $prices = [];
        $timestamp = new \DateTimeImmutable();

        foreach ($this->allowedPairs as $pair) {
            try {
                $response = $this->client->request('GET', $this->binanceApiBaseUrl . '/api/v3/ticker/price', ['query' => ['symbol' => $pair]]);
                if ($response->getStatusCode() !== Response::HTTP_OK) {
                    $this->logger->error('Binance API request failed', ['pair' => $pair, 'status' => $response->getStatusCode()]);
                    continue;
                }
                $data = $response->toArray();
                if (isset($data['price']) && (is_numeric($data['price']) || is_string($data['price']))) {
                    $prices[] = new ExchangePriceDto($pair, (string)$data['price'], $timestamp);
                }
            } catch (\Throwable $e) {
                $this->logger->critical('Failed to fetch price', ['pair' => $pair, 'message' => $e->getMessage()]);
            }
        }
        if (empty($prices)) {
            throw new \Exception('Could not fetch any rates.');
        }

        return $prices;
    }

    /**
     * @return list<non-empty-string>
     */
    public function getSupportedPairs(): array
    {
        return $this->allowedPairs;
    }
}
