<?php

namespace App\Controller;

use App\Game\GameManager;
use App\Game\BettingManager;
use App\Game\BankerInterface;
use App\Game\EasyBanker;
use App\Game\MediumBanker;
use App\Game\HardBanker;
use App\Game\Player;
use App\Card\DeckOfCards;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TwentyOneGameController extends AbstractController
{
    #[Route("/game", name: "game_index", methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('game/index.html.twig');
    }

    #[Route("/game/docs", name: "game_docs", methods: ['GET'])]
    public function docs(): Response
    {
        return $this->render('game/docs.html.twig');
    }

    #[Route("/game/init", name: "game_init", methods: ['GET'])]
    public function init(): Response
    {
        /** @var mixed[] $bankers Data to display in difficulty level select form. */
        $bankers = [
            'easy' => [
                'name' => 'BetBot Buddy',
                'img' => 'easy_banker.png',
                'description' => 'This is your friendly casino banker who does not know anything about counting cards. They always play until they hit 17 or more.',
            ],
            'medium' => [
                'name' => 'Cashbot McMoneybags',
                'img' => 'medium_banker.png',
                'description' => 'This banker is a master card counter. They will hit until they are likely to burst.',
            ],
            'hard' => [
                'name' => 'Robo-Schemer Scrooge',
                'img' => 'hard_banker.png',
                'description' => 'This money grubbing fiend is definitely looking at your cards. They will hit as long as they are behind.',
            ],
        ];

        $data = [
            'bankers' => $bankers,
        ];

        return $this->render('game/init.html.twig', $data);
    }

    /** @SuppressWarnings(PHPMD.ElseExpression) */
    #[Route("/game/init", name: "game_init_post", methods: ['POST'])]
    public function setup(Request $request, SessionInterface $session): Response
    {
        // Start by checking and determining banker difficulty
        $level = $request->request->get('banker', null);

        /** @var BankerInterface|null $banker */
        $banker = null;

        if ($level === 'easy') {
            $banker = new EasyBanker();
        } elseif ($level === 'medium') {
            $banker = new MediumBanker();
        } elseif ($level === 'hard') {
            $banker = new HardBanker();
        } else {
            $this->addFlash(
                'warning',
                'No difficulty level selected.'
            );
            return $this->redirectToRoute('game_init');
        }

        /** @var GameManager $gameManager */
        $gameManager = new GameManager();

        $gameManager->setBanker($banker);
        $gameManager->setPlayer(new Player());

        /** @var DeckOfCards $deck */
        $deck = new DeckOfCards();

        $deck->shuffleCards();
        $gameManager->setDeck($deck);

        // Set assistance mode on or off
        $assistance = $request->request->get('assistance', false) !== false;
        $gameManager->setAssistanceMode($assistance);

        // First card is mandatory
        $gameManager->dealPlayer();

        // Save state
        $session->set('gameManager', $gameManager);

        /** @var BettingManager $bettingManager */
        $bettingManager = new BettingManager();

        // Set betting mode on or off
        $betting = $request->request->get('betting', false) !== false;
        $bettingManager->setBetting($betting);

        $session->set('bettingManager', $bettingManager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play", name: "game_play")]
    public function play(SessionInterface $session): Response
    {
        /** @var GameManager|null $gameManager */
        $gameManager = $session->get('gameManager', null);

        // Start by checking if gameManager is set up properly
        if ($gameManager === null) {
            $this->addFlash(
                'warning',
                'No active game. Press "Play" to start a new one.'
            );
            return $this->redirectToRoute('game_index');
        }

        /** @var BettingManager $bettingManager */
        $bettingManager = $session->get('bettingManager');

        // Get state to display in view
        $data = array_merge($gameManager->getState(), $bettingManager->getState());

        return $this->render('game/play.html.twig', $data);
    }

    #[Route("/game/play/hit", name: "game_hit", methods: ['POST'])]
    public function hit(SessionInterface $session): Response
    {
        /** @var GameManager $gameManager */
        $gameManager = $session->get('gameManager');

        // Player hit for another card
        $gameManager->dealPlayer();

        /** @var BettingManager $bettingManager */
        $bettingManager = $session->get('bettingManager');

        if ($bettingManager->isBetting() === true) {
            $hasWon = $gameManager->getHasWon();

            if ($hasWon === -1) {
                $bettingManager->bankerWinsStake();
            } elseif ($hasWon === 1) {
                $bettingManager->playerWinsStake();
            }

            $session->set('bettingManager', $bettingManager);
        }

        $session->set('gameManager', $gameManager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play/stay", name: "game_stay", methods: ['POST'])]
    public function stay(SessionInterface $session): Response
    {
        /** @var GameManager $gameManager */
        $gameManager = $session->get('gameManager');

        // Player decide to stay, banker takes their turn
        $gameManager->dealBanker();

        /** @var BettingManager $bettingManager */
        $bettingManager = $session->get('bettingManager');

        if ($bettingManager->isBetting() === true) {
            $hasWon = $gameManager->getHasWon();

            if ($hasWon === -1) {
                $bettingManager->bankerWinsStake();
            } elseif ($hasWon === 1) {
                $bettingManager->playerWinsStake();
            }

            $session->set('bettingManager', $bettingManager);
        }

        $session->set('gameManager', $gameManager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play/reset", name: "game_reset", methods: ['POST'])]
    public function reset(SessionInterface $session): Response
    {
        /** @var GameManager $gameManager */
        $gameManager = $session->get('gameManager');

        $gameManager->reset();

        $gameManager->dealPlayer();

        $session->set('gameManager', $gameManager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play/bet", name: "game_bet", methods: ['POST'])]
    public function bet(Request $request, SessionInterface $session): Response
    {
        /** @var BettingManager $bettingManager */
        $bettingManager = $session->get('bettingManager');

        $bet = intval($request->request->get('bet'));

        if ($bettingManager->placeBet($bet) === false) {
            $this->addFlash(
                'warning',
                'Invalid betting amount!'
            );
        }

        $session->set('bettingManager', $bettingManager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/destroy", name: "game_destroy", methods: ['POST'])]
    public function destroy(SessionInterface $session): Response
    {
        $session->remove('gameManager');
        $session->remove('bettingManager');

        return $this->redirectToRoute('game_index');
    }
}
