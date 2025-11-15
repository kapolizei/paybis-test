<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CryptoRate;

class CryptoRateFetcher
{
    private $logger;
    private $entityManager;
    private $apiKey;


    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->apiKey = $params->get('API_KEY');
    }

    public function fetchRates(string $symbol, string $quote, array $symbols = []): array
    {
        $client = HttpClient::create();

        $symbolsToFetch = !empty($symbols) ? $symbols : [$symbol];

        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';

        $query = [
            'symbol' => implode(',', $symbolsToFetch),
            'convert' => $quote,
        ];

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-CMC_PRO_API_KEY' => $this->apiKey,
                ],
                'query' => $query,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException('Error fetching rates: ' . $response->getContent(false));
            }

            $responseData = $response->toArray();
            $data = $responseData['data'] ?? [];

            foreach ($data as $symbolKey => $item) {
                $entity = new CryptoRate();
                $entity->setCurrencyPair($item['symbol']);
                $entity->setQuoteCurrency($quote);

                if (isset($item['quote'][$quote]['price'])) {
                    $entity->setRate($item['quote'][$quote]['price']);
                }

                // last_updated находится внутри quote
                $timestamp = new \DateTime($item['quote'][$quote]['last_updated']);
                $entity->setTimestamp($timestamp);

                $this->entityManager->persist($entity);

                $this->logger->info("Saved rate for {$item['symbol']}: {$item['quote'][$quote]['price']}");
            }

            $this->entityManager->flush();

            return $data;

        } catch (\Throwable $e) {
            $this->logger->error('Failed to fetch crypto rates', [
                'error' => $e->getMessage(),
                'url' => $url,
                'query' => $query,
            ]);
            throw $e;
        }
    }
}
