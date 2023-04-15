<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TwentyOneGameController extends AbstractController
{
    #[Route("/game", name: "game_index")]
    public function index(SessionInterface $session): Response
    {
        return $this->render('game/index.html.twig');
    }

    #[Route("/game/init", name: "game_init", methods: ['GET'])]
    public function init(SessionInterface $session): Response
    {
        $bankers = [
            'easy' => [
                'name' => 'BetBot Buddy',
                'img' => 'easy_banker.png',
                'description' => 'This is your friendly casino banker who does not know anything about counting cards.'
            ],
            'medium' => [
                'name' => 'Cashbot McMoneybags',
                'img' => 'medium_banker.png',
                'description' => 'This banker graduated from Robo Bankers University with top marks in all subjects, including statistics.'
            ],
            'hard' => [
                'name' => 'Robo-Schemer Scrooge',
                'img' => 'hard_banker.png',
                'description' => 'This money grubbing fiend is definitely looking at your cards.'
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
        $session->set('assistance', $request->request->get('assistance', 'off'));
        $session->set('betting', $request->request->get('betting', 'off'));
        $session->set('banker', $request->request->get('banker', 'none'));

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/play", name: "game_play")]
    public function play(SessionInterface $session): Response
    {

        $data = [
            'assistance' => $session->get('assistance'),
            'betting' => $session->get('betting'),
            'banker' => $session->get('banker'),
        ];

        return $this->render('game/play.html.twig', $data);
    }

}
