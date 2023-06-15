<?php

namespace App\Adventure\Utils;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Game\Location;
use App\Entity\Game\Item;
use App\Entity\Game\Action;
use App\Entity\Game\Response;
use App\Entity\Game\Connection;
use App\Adventure\Location as GameLocation;
use App\Adventure\Item as GameItem;
use App\Adventure\Game;
use Exception;

/**
 * Handles setting up the game from the database.
 */
class GameSetupManager {

    private $entityManager;

    /**
     * GameSetupManager constructor.
     *
     * @param EntityManagerInterface $entityManager The Doctrine entity manager.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieves the fully qualified class name for the action based on the provided type.
     *
     * @param string $type The type of the action.
     *
     * @return string|null The fully qualified class name if it exists, or null if not found.
     */
    private function getActionClass(string $type): ?string 
    {
        $namespace = 'App\Adventure\InputActions';

        $actionClasses = [
            'examine' => 'ExamineAction',
            'take' => 'TakeAction',
            'use' => 'UseAction',
        ];

        $type = trim(strtolower($type));

        $actionClass = $namespace . '\\' . $actionClasses[$type];

        if (class_exists($actionClass)) {
            return $actionClass;
        }

        return null;
    }

    /**
     * Retrieves the fully qualified class name for the response based on the provided type.
     *
     * @param string $type The type of the response.
     *
     * @return string|null The fully qualified class name if it exists, or null if not found.
     */
    private function getResponseClass(string $type): ?string
    {
        $namespace = 'App\Adventure\ActionResponses';

        $responseClasses = [
            'swap' => 'SwapLocationResponse',
            'move' => 'MoveLocationResponse',
        ];

        $type = trim(strtolower($type));

        $responseClass = $namespace . '\\' . $responseClasses[$type];

        if (class_exists($responseClass)) {
            return $responseClass;
        }

        return null;
    }

    /**
     * Sets up the game from the database.
     *
     * @return Game The configured game instance.
     * @throws Exception If any error occurs during the setup process.
     */
    public function setupGameFromDatabase(): Game
    {
        // Fetch data from database
        $locationData = $this->entityManager->getRepository(Location::class)->findAll();
        $itemData = $this->entityManager->getRepository(Item::class)->findAll();
        $actionData = $this->entityManager->getRepository(Action::class)->findAll();
        $responseData = $this->entityManager->getRepository(Response::class)->findAll();
        $connectionData = $this->entityManager->getRepository(Connection::class)->findAll();

        // Lookup arrays for easy access
        $locationLookup = [];
        $itemsLookup = [];
        $actionsLookup = [];

        // Create locations and populate lookup array
        foreach ($locationData as $data) {
            $location = new GameLocation($data->getName(), $data->getDescription(), $data->getDetails());
            $locationLookup[$data->getId()] = $location;
        }

        // Create items and place them in locations, and populate lookup array
        foreach ($itemData as $data) {
            $item = new GameItem($data->getName(), $data->getDescription());
            $itemLookup[$data->getId()] = $item;

            // Place item in location
            $inLocation = $locationLookup[$data->getLocationId()];
            $inLocation->addItem($item);
        }

        // Create actions and add them to the corresponding items, and populate lookup array
        foreach ($actionData as $data) {
            // Retrieve the corrsponging action class
            $actionClass = $this->getActionClass($data->getType());

            if ($actionClass === null) {
                throw new Exception('Action class not found for type: ' . $data->getType());
            }

            $action = new $actionClass($data->getDescription());

            // Set required location, if applicable
            $locationId = $data->getRequiredLocationId();
            if ($locationId) {
                $requiredLocation = $locationLookup[$locationId];
                $action->setRequiredLocation($requiredLocation);
            }

            // Add action to item
            $item = $itemLookup[$data->getItemId()];
            $item->addAction($action);

            $actionLookup[$data->getId()] = $action;
        }

        // Create responses and add them to the corresponding actions
        foreach ($responseData as $data) {
            // Retrieve the corrsponging response class
            $responseClass = $this->getResponseClass($data->getType());

            if ($responseClass === null) {
                throw new Exception('Response class not found for type: ' . $data->getType());
            }

            // Add response location to either swap in or move to when action is triggered
            $location = $locationLookup[$data->getLocationId()];
            $response = new $responseClass($location);

            // Add response to action
            $action = $actionLookup[$data->getActionId()];
            $action->setLocationResponse($response);
        }

        // Create connections between locations
        foreach ($connectionData as $data) {
            $fromLocation = $locationLookup[$data->getFromLocationId()];
            $toLocation = $locationLookup[$data->getToLocationId()];
            $direction = trim(strtolower($data->getDirection()));

            $fromLocation->connectTo($toLocation, $direction);
        }

        // Create game instance and set starting location
        $game = new Game();
        $game->setCurrentLocation($locationLookup[1]);

        return $game;
    }
}
