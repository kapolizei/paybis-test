<?php
// src/Controller/ApiController.php
namespace App\Controller;

use App\Entity\CryptoRate;
use App\Service\CryptoRateFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

     #[Route('/fetch/{symbol}/{quote}/', name: 'fetch')]
    public function fetch(string $symbol, string $quote, CryptoRateFetcher $cryptoRateFetcher): JsonResponse
    {

        if ($symbol === "all") {
            $symbols = ['BTC', 'ETH', 'XRP', 'SOL'];
            $cryptoRateFetcher->fetchRates('', $quote, $symbols);
            $fromDb = $this->findMany($symbols, $quote);
        } else {
            $fromDb = $this->findOne($symbol, $quote);
        }
        return new JsonResponse($fromDb);

    }

    private function findMany(array $symbols, string $quote): array
    {
        $symbols = ['BTC', 'ETH', 'XRP', 'SOL'];
        $responseData = [];

        foreach ($symbols as $symbol) {
            $cryptoRate = $this->entityManager->getRepository(CryptoRate::class)->findOneBy([
                'currencyPair' => $symbol, 
                'quoteCurrency' => $quote
            ],['id' => 'DESC']);
            $responseData[$symbol] = $cryptoRate ? $cryptoRate->exportToArray() : null;
        }
        return $responseData;
    }

    private function findOne($symbol, $quote): array
    {
        $responseData = [];
        $cryptoRate = $this->entityManager->getRepository(CryptoRate::class)->findOneBy([
            'currencyPair' => $symbol,
            'quoteCurrency' => $quote,
        ], ['id' => 'DESC']);

        $responseData[$symbol] = $cryptoRate ? $cryptoRate->exportToArray() : null;
        return $responseData;
    }

}
