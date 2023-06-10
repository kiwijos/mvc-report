<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Adventure\LocationNavigator;
use App\Adventure\Location;
use App\Adventure\Item;
use App\Adventure\InputActions\ExamineAction;
use App\Adventure\InputActions\TakeAction;
use App\Adventure\InputActions\UseAction;
use App\Adventure\ActionResponses\UnhideLocationResponse;
use App\Adventure\ActionResponses\ConnectLocationResponse;
use App\Adventure\Utils\Unpacker;
use App\Adventure\Log;
use App\Adventure\Inventory;
use App\Adventure\Game;

class AdventureController extends AbstractController
{
    #[Route('/proj/game/init', name: 'setup_game')]
    public function init(SessionInterface $session)
    {
        $game = new Game();
        $inventory = new Inventory();

        $game->setInventory($inventory);

        // Instantiate locations
        $cyberspaceCity = new Location(
            'Cyberspace City',
            'A bustling virtual metropolis.',
            'A bustling digital metropolis with neon lights and towering skyscrapers, where hackers and corporations vie for control',    
        );

        $dungeon = new Location(
            'Digital Dungeon',
            'A dark and eerie underground lair.',
            'A dark and mysterious virtual realm, home to cryptic puzzles, hidden secrets, and guardian programs.',
        );

        $hiddenRoom = new Location(
            'Hidden Room',
            'A hidden room',
            'A very hidden rooom',
        );

        $key = new Item('key', 'A weathered key lays in the corner.');
        $examineKey = new ExamineAction('As you closely examine the key, you notice its weathered surface, revealing years of use and countless adventures. Its intricate design hints at a hidden purpose.');
        $takeKey = new TakeAction('You take the weathered key, its weight hinting at the countless possibilities it may unlock.');
        $useKey = new UseAction('You jam the key into a lock on one of the doors. The door clicks open!');

        $connectHiddenRoom = new ConnectLocationResponse($hiddenRoom, 'west');

        $useKey->setRequiredLocation($cyberspaceCity);
        $useKey->setLocationResponse($connectHiddenRoom);

        $key->addAction($examineKey);
        $key->addAction($takeKey);
        $key->addAction($useKey);

        $dungeon->addItem($key);

        // Connect locations
        $cyberspaceCity->connectTo($dungeon, 'north');
        $dungeon->connectTo($cyberspaceCity, 'south');

        // Set the initial location
        $game->setCurrentLocation($cyberspaceCity);

        $session->set('game', $game);

        $log = $session->get('game_log', new Log());

        $log->addEntry(Unpacker::unpackLocationDescriptions($cyberspaceCity));

        $session->set('game_log', $log);

        return $this->redirectToRoute('render_location');
    }

    #[Route('/proj/game/location', name: 'render_location')]
    public function renderLocation(SessionInterface $session)
    {
        $log = $session->get('game_log', new Log());

        return $this->render('proj/location.html.twig', [ 'log' => $log, ]);
    }

    #[Route('/proj/game/action', name: 'perform_action', methods: ['POST'])]
    public function performAction(SessionInterface $session, Request $request)
    {
        $log = $session->get('game_log', new Log());

        $userInput = $request->get('input');

        $log->addEntry($userInput); // Store input as entry

        // Split the input into action and target
        $parts = explode(' ', $userInput, 2);

        $action = $parts[0] ?? ''; // Get the first part as action, e.g. 'go'
        $target = $parts[1] ?? ''; // Get the second part as target, e.g. 'south'

        if ($action === 'quit') {
            return $this->redirectToRoute('reset');
        }

        $game = $session->get('game');

        $result = $game->processAction($action, $target);

        $log->addEntry($result);

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
