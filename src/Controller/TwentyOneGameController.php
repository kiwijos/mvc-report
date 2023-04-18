<?php

namespace App\Controller;

use App\Game\GameManager;
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
    public function index(SessionInterface $session): Response
    {
        return $this->render('game/index.html.twig');
    }

    #[Route("/game/docs", name: "game_docs", methods: ['GET'])]
    public function docs(SessionInterface $session): Response
    {
        return $this->render('game/docs.html.twig');
    }

    #[Route("/game/init", name: "game_init", methods: ['GET'])]
    public function init(SessionInterface $session): Response
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

    #[Route("/game/init", name: "game_init_post", methods: ['POST'])]
    public function init_post(Request $request, SessionInterface $session): Response
    {
        // Start by checking and determining banker difficulty
        $level = $request->request->get('banker', null);

        $banker;

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

        // Set up game manager
        $manager = new GameManager();

        $manager->setBanker($banker);
        $manager->setPlayer(new Player());
        
        $deck = new DeckOfCards();
        $deck->shuffleCards();
        $manager->setDeck($deck);

        // Set assistance mode on or off
        $assistance = $request->request->get('assistance', false) !== false;
        $manager->setAssistanceMode($assistance);

        // First card is mandatory
        $manager->dealPlayer();

        // Save state
        $session->set('manager', $manager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play", name: "game_play")]
    public function play(SessionInterface $session): Response
    {
        // Start by checking if manager is set up properly
        $manager = $session->get('manager', null);

        if ($manager === null) {
            $this->addFlash(
                'warning',
                'No active game. Press "Play" to start a new one.'
            );
            return $this->redirectToRoute('game_index');
        }

        // Get state to display in view
        $data = $manager->getState();

        return $this->render('game/play.html.twig', $data);
    }

    #[Route("/game/play/hit", name: "game_hit", methods: ['POST'])]
    public function hit(Request $request, SessionInterface $session): Response
    {
        // Player hit for another card
        $manager = $session->get('manager');

        $manager->dealPlayer();

        $session->set('manager', $manager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play/stay", name: "game_stay", methods: ['POST'])]
    public function stay(Request $request, SessionInterface $session): Response
    {
        // Player decide to stay, banker takes their turn
        $manager = $session->get('manager');

        $manager->dealBanker();

        $session->set('manager', $manager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play/reset", name: "game_reset", methods: ['POST'])]
    public function reset(Request $request, SessionInterface $session): Response
    {
        $manager = $session->get('manager');

        $manager->reset();

        $manager->dealPlayer();

        $session->set('manager', $manager);

        return $this->redirectToRoute('game_play');
    }

}
