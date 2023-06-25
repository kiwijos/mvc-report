<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Adventure\Utils\GameSetupManager;
use App\Adventure\Utils\Unpacker;
use App\Adventure\Log;
use App\Adventure\Inventory;

class AdventureController extends AbstractController
{
    #[Route('/proj/game', name: 'start_game')]
    public function start() {
        // Don't log this message
        $message = [
            "Welcome! You are about to embark on an epic adventure.\n" .
            "To begin, type 'start' (or 'restart'). You can use this action during the game to start over from the beginning.\n\n" .
            "Remember, at any time, you can type 'help' for assistance. Good luck!\n"
        ];

        return $this->render('proj/location.html.twig', [ 'entries' => $message]);
    }

    #[Route('/proj/game/init', name: 'setup_game')]
    public function init(SessionInterface $session, ManagerRegistry $doctrine)
    {
        // Set up game from database
        $entityManager = $doctrine->getManager('game');
        $gameManager = new GameSetupManager($entityManager);
        $game = $gameManager->setupGameFromDatabase();
        
        // Set the player's inventory 
        $inventory = new Inventory();
        $game->setInventory($inventory);

        $session->set('game', $game); // Save the configured game instance

        // Create a new log
        $log = new Log();

        // Retrieve the starting location
        $startingLocation = $game->getCurrentLocation();

        // Add some instructions after the first location's description to help player get started
        $message = Unpacker::unpackLocationDescriptions($startingLocation) . "\n";
        $message .= "You can have a closer look at objects in the scene by typing 'examine' followed by the name of the object. Interactable objects are enclosed in brackets [name].";
        
        $log->addEntry($message); // Save to log

        $session->set('game_log', $log);

        return $this->redirectToRoute('render_location');
    }

    #[Route('/proj/game/location', name: 'render_location')]
    public function renderLocation(SessionInterface $session)
    {
        // Get logged entries
        $log = $session->get('game_log', new Log());
        $entries = $log->getEntries();

        return $this->render('proj/location.html.twig', [ 'entries' => $entries, ]);
    }

    #[Route('/proj/game/action', name: 'perform_action', methods: ['POST'])]
    public function performAction(SessionInterface $session, Request $request)
    {
        $userInput = $request->get('input');

        // Split the input into action and target
        $parts = explode(' ', $userInput, 2);

        $action = $parts[0] ?? ''; // Get the first part as action, e.g. 'go'
        $target = $parts[1] ?? ''; // Get the second part as target, e.g. 'south'

        // Restart 
        if ($action === 'start' || $action === 'restart') {
            $session->invalidate();

            return $this->redirectToRoute('setup_game');
        }

        $log = $session->get('game_log', new Log());

        $log->addEntry($userInput); // Save input to log

        // Retrieve game object to process action
        $game = $session->get('game');
        $result = $game->processAction($action, $target);

        $log->addEntry($result); // Save response to log

        $session->set('game', $game);
        $session->set('game_log', $log);

        return $this->redirectToRoute('render_location');
    }
}
