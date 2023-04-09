<?php

namespace App\Controller;
use App\Card\DeckOfCards;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardGameControllerJson
{
    #[Route("/api/deck", name: "json_deck", methods: ['GET'])]
    public function jsonDeck(SessionInterface $session): JsonResponse
    {
        $deck = new DeckOfCards();
        $session->set('currentDeck', $deck);

        $data = [
            "deck" => $deck->getString(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/api/deck/shuffle", name: "json_shuffle", methods: ['POST'])]
    public function jsonShuffle(SessionInterface $session): JsonResponse
    {
        $deck = new DeckOfCards();
        
        $deck->shuffleCards();
        
        $session->set('currentDeck', $deck);

        $data = [
            "deck" => $deck->getString(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        );
        return $response;
    }
}
