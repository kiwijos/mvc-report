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

        // Retrieve log or, more likely, create a new log
        $log = $session->get('game_log', new Log());

        // Retrieve the starting location
        $startingLocation = $game->getCurrentLocation();

        // Add instructions around the first location's description.
        $instructions = "Welcome! You are about to embark on an epic adventure. Remember, at any time, you can type 'help' for assistance. Good luck!\n\n";
        $instructions .= Unpacker::unpackLocationDescriptions($startingLocation) . "\n";
        $instructions .= "You can have a closer look at objects in the scene by typing 'examine' followed by the name of the object. Interactable objects are enclosed in brackets [name]";
        
        $log->addEntry($instructions); // Save to log

        $session->set('game_log', $log);

        return $this->redirectToRoute('render_location');
    }

    #[Route('/proj/game/location', name: 'render_location')]
    public function renderLocation(SessionInterface $session)
    {
        $log = $session->get('game_log', new Log()); // Get logged entries

        return $this->render('proj/location.html.twig', [ 'log' => $log, ]);
    }

    #[Route('/proj/game/action', name: 'perform_action', methods: ['POST'])]
    public function performAction(SessionInterface $session, Request $request)
    {
        $log = $session->get('game_log', new Log());

        $userInput = $request->get('input');

        $log->addEntry($userInput); // Save input to log

        // Split the input into action and target
        $parts = explode(' ', $userInput, 2);

        $action = $parts[0] ?? ''; // Get the first part as action, e.g. 'go'
        $target = $parts[1] ?? ''; // Get the second part as target, e.g. 'south'

        // This aciton is not visible to the player
        if ($action === 'restart') {
            return $this->redirectToRoute('reset');
        }

        // Retrive game object to process action
        $game = $session->get('game');
        $result = $game->processAction($action, $target);

        $log->addEntry($result); // Save action response to log

        $session->set('game', $game);
        $session->set('game_log', $log);

        return $this->redirectToRoute('render_location');
    }

    #[Route('/proj/game/reset', name: 'reset', methods: ['GET'])]
    public function resetSession(SessionInterface $session)
    {
        // Invalidate the session
        $session->invalidate();

        // Regenerate the session ID to enhance security
        $session->migrate();

        return $this->redirectToRoute('setup_game');
    }
}
