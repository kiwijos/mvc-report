<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Card\Card;
use App\Game\GameManager;
use App\Game\BettingManager;

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
        /** @var DeckOfCards $deck */
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
        /** @var DeckOfCards $deck */
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

    #[Route("/api/deck/draw/{number<\d+>}", name: "json_draw_many", methods: ['POST'])]
    public function jsonDrawMany(SessionInterface $session, int $number): JsonResponse
    {
        /** @var DeckOfCards $deck */
        $deck = $session->get('currentDeck', new DeckOfCards());

        $draw = array_map('strval', $deck->draw($number));

        $session->set('currentDeck', $deck); // Save updated deck

        $data = [
            "draw"  => $draw,
            "count" => $deck->getCount(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/api/deck/draw", name: "json_draw", methods: ['POST'])]
    public function jsonDraw(SessionInterface $session): JsonResponse
    {
        /** @var DeckOfCards $deck */
        $deck = $session->get('currentDeck', new DeckOfCards());

        $draw = array_map('strval', $deck->draw(1));

        $session->set('currentDeck', $deck); // Save updated deck

        $data = [
            "draw"  => $draw,
            "count" => $deck->getCount(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/api/deck/deal/{players<\d+>}/{cards<\d+>}", name: "json_deal", methods: ['POST'])]
    public function jsonDeal(SessionInterface $session, int $players, int $cards): JsonResponse
    {
        /** @var DeckOfCards $deck */
        $deck = $session->get('currentDeck', new DeckOfCards());

        $deal = [];

        for ($i = 0; $i < $players; $i++) {
            $deal[] = array_map('strval', $deck->draw($cards));
        }

        $session->set('currentDeck', $deck); // Save updated deck

        $data = [
            "deal"  => $deal,
            "count" => $deck->getCount(),
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/api/game", name: "json_game", methods: ['GET'])]
    public function jsonGame(SessionInterface $session): JsonResponse
    {
        /** @var GameManager|null $gameManager */
        $gameManager = $session->get('gameManager', null);

        if ($gameManager === null) {
            return new JsonResponse(['notice' => 'No active game to display']);
        }

        $gameState = $gameManager->getState();

        /** @var Card[] $playerCards */
        $playerCards = $gameState['playerCards'];

        /** @var Card[] $bankerCards */
        $bankerCards = $gameState['bankerCards'];

        // Represent card objects as strings
        $gameState['playerCards'] = array_map('strval', $playerCards);
        $gameState['bankerCards'] = array_map('strval', $bankerCards);

        // Turn assistance number into a sensible message
        $gameState['assistance'] = "Player has {$gameState['assistance']}% risk of bursting";

        // Turn has won status number into a sensible message
        $gameState['hasWon'] = ($gameState['hasWon'] === 1) ? 'Player won' : (($gameState['hasWon'] === -1) ? 'Banker won' : 'No winner');

        /** @var BettingManager|null $bettingManager */
        $bettingManager = $session->get('bettingManager', null);

        // Get state of betting... but only if betting is on
        if ($bettingManager !== null and $bettingManager->isBetting() === true) {
            $bettingState = $bettingManager->getState();

            $gameState['playerCoins'] = $bettingState['playerCoins'];
            $gameState['bankerCoins'] = $bettingState['bankerCoins'];
            $gameState['currentBet'] = $bettingState['stake'];
            $gameState['gameOver'] = $bettingState['gameOver'] ? 'Yes' : 'No';
        }

        $data = $gameState;

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        );

        return $response;
    }
}
