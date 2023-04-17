<?php

namespace App\Controller;

use App\Game\GameManager;
use App\Game\EasyBanker;
use App\Game\Player;

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
        $bankers = [
            'easy' => [
                'name' => 'BetBot Buddy',
                'img' => 'easy_banker.png',
                'description' => 'This is your friendly casino banker who does not know anything about counting cards. They always play until they hit 17 or more.',
            ],
            'medium' => [
                'name' => 'Cashbot McMoneybags',
                'img' => 'medium_banker.png',
                'description' => 'This banker graduated from Robo Bankers University with top marks in all subjects, including statistics. They always count their own cards.',
            ],
            'hard' => [
                'name' => 'Robo-Schemer Scrooge',
                'img' => 'hard_banker.png',
                'description' => 'This money grubbing fiend is definitely looking at your cards.',
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
        $level = $request->request->get('banker', null);
        
        $banker;

        if ($level === 'easy') {
            $banker = new EasyBanker();
        } elseif ($level === 'medium') {
            // $banker = new MediumBanker();
        } elseif ($level === 'hard') {
            // $banker = new HardBanker();
        } else {
            return $this->redirectToRoute('game_init');
        }

        $manager = new GameManager();

        $manager->setBanker($banker);
        $manager->setPlayer(new Player());

        $manager->dealPlayer();

        $session->set('manager', $manager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play", name: "game_play")]
    public function play(SessionInterface $session): Response
    {
        $manager = $session->get('manager', null);

        if ($manager === null) {
            return $this->redirectToRoute('game_index');
        }

        $data = $manager->getState();

        return $this->render('game/play.html.twig', $data);
    }

    #[Route("/game/play/hit", name: "game_hit", methods: ['POST'])]
    public function hit(Request $request, SessionInterface $session): Response
    {
        $manager = $session->get('manager', null);

        if ($manager === null) {
            return $this->redirectToRoute('game_index');
        }

        $manager->dealPlayer();

        $session->set('manager', $manager);

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play/stay", name: "game_stay", methods: ['POST'])]
    public function stay(Request $request, SessionInterface $session): Response
    {
        $manager = $session->get('manager', null);

        if ($manager === null) {
            return $this->redirectToRoute('game_index');
        }

        $manager->dealBanker();

        $session->set('manager', $manager);

        return $this->redirectToRoute('game_play');
    }

}
