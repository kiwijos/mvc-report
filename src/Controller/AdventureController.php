<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Adventure\Player;
use App\Adventure\Location;

class AdventureController extends AbstractController
{
    #[Route("/proj/game/init", name: "setup_game")]
    public function init(SessionInterface $session)
    {
        // Instantiate the player
        $player = new Player();

        // Instantiate locations
        $cyberspaceCity = new Location('Cyberspace City', 'A bustling virtual metropolis.');
        $dungeon = new Location('Digital Dungeon', 'A dark and eerie underground lair.');

        // Connect locations
        $cyberspaceCity->connectTo('north', $dungeon);
        $dungeon->connectTo('south', $cyberspaceCity);

        // Set the initial location
        $player->setLocation($cyberspaceCity);

        $session->set('player', $player);

        return $this->redirectToRoute('render_location');
    }

    #[Route("/proj/game/location", name: "render_location")]
    public function renderLocation(SessionInterface $session)
    {
        $player = $session->get('player');

        $location = $player->getCurrentLocation();

        return $this->render('adventure/location.html.twig', [ 'location' => $location, ]);
    }

    #[Route("/proj/game/action", name: "perform_action", methods: ['POST'])]
    public function performAction(SessionInterface $session, Request $request)
    {
        $player = $session->get('player');

        $input = $request->get('input');

        // Split the input into action and target
        $parts = explode(' ', $input, 2);

        $action = $parts[0] ?? ''; // Get the first part as action
        $target = $parts[1] ?? ''; // Get the second part as target

        $player->performAction($action, $target);

        return $this->redirectToRoute('render_location');
    }
}
