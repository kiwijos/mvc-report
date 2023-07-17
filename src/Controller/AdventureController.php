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
use App\Adventure\Location;
use App\Adventure\Game;

class AdventureController extends AbstractController
{
    #[Route('/proj/game', name: 'start_game')]
    public function start(SessionInterface $session, Request $request): Response
    {
        $session->invalidate(); // Make sure session is cleared before attempting to start a new game

        // Check if the player has been directed here with a message
        $message = $request->query->get('message', null);

        if ($message === null) {
            // Show default message otherwise
            $message =
                "Welcome! You are about to embark on an epic adventure.\n" .
                "To begin, type 'start' (or 'restart'). You can use this action during the game to start over from the beginning.\n\n" .
                "Remember, at any time during the game, you can type 'help' for assistance. Good luck!\n";
        }

        return $this->render('proj/location.html.twig', [
            'entries' => [$message],
            'inputs' => [],
        ]);
    }

    #[Route('/proj/game/init', name: 'setup_game')]
    public function init(SessionInterface $session, ManagerRegistry $doctrine): Response
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

        /** @var Location */
        $startingLocation = $game->getCurrentLocation(); // Get the starting location

        // Add some instructions after the first location's description to help the player get started
        $message = Unpacker::unpackLocationDescriptions($startingLocation) . "\n";
        $message .= "You can have a closer look at objects in the scene by typing 'examine' followed by the name of the object. Interactable objects are enclosed in brackets [name].";

        $log->addEntry($message); // Save to log

        $session->set('game_log', $log);

        return $this->redirectToRoute('render_location');
    }

    #[Route('/proj/game/location', name: 'render_location')]
    public function renderLocation(SessionInterface $session): Response
    {
        /** @var Log */
        $log = $session->get('game_log', new Log()); // Get all logged entries
        $entries = $log->getEntries();

        /** @var Log */
        $inputLog = $session->get('input_log', new Log()); // Get logged inputs separately
        $inputs = $inputLog->getEntries();

        return $this->render('proj/location.html.twig', [
            'entries' => $entries,
            'inputs' => $inputs,
        ]);
    }

    #[Route('/proj/game/action', name: 'perform_action', methods: ['POST'])]
    public function performAction(SessionInterface $session, Request $request): Response
    {
        /** @var string */
        $userInput = $request->get('input');

        // Split the input into action and target
        $parts = explode(' ', $userInput, 2);

        $action = $parts[0] ?? ''; // Get the first part as action, e.g. 'go'
        $target = $parts[1] ?? ''; // Get the second part as target, e.g. 'south'

        // Restart the game
        if ($action === 'start' || $action === 'restart') {
            $session->invalidate();

            return $this->redirectToRoute('setup_game');
        }

        /** @var Game|null */
        $game = $session->get('game', null); // Retrieve game object

        // Check game before attempting to process action, redirect if not found
        if ($game === null) {
            $message = "Please start a new game by typing 'start'.";

            return $this->redirectToRoute('start_game', ['message' => $message]);
        }

        /** @var Log */
        $inputLog = $session->get('input_log', new Log()); // Only for logging user input

        /** @var Log */
        $log = $session->get('game_log', new Log());

        $inputLog->addEntry($userInput); // Save input
        $session->set('input_log', $inputLog);

        $log->addEntry($userInput); // Save input to the main log too

        $result = $game->processAction($action, $target);
        $log->addEntry($result); // Save response to the main log

        $session->set('game', $game);
        $session->set('game_log', $log);

        return $this->redirectToRoute('render_location');
    }
}
