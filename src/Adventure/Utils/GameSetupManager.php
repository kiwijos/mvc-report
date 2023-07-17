<?php

namespace App\Adventure\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Game\Location;
use App\Entity\Game\Item;
use App\Entity\Game\Action;
use App\Entity\Game\Response;
use App\Entity\Game\Connection;
use App\Adventure\Location as GameLocation;
use App\Adventure\Item as GameItem;
use App\Adventure\Game;
use App\Adventure\ActionResponses\LocationResponseInterface;
use App\Adventure\InputActions\ExamineAction;
use App\Adventure\InputActions\TakeAction;
use App\Adventure\InputActions\UseAction;
use Exception;

/**
 * Handles setting up the game from the database.
 */
class GameSetupManager
{
    /**
     * @var EntityManagerInterface|ObjectManager
     */
    private $entityManager;

    /**
     * GameSetupManager constructor.
     *
     * @param EntityManagerInterface|ObjectManager $entityManager The Doctrine entity manager.
     */
    public function __construct(EntityManagerInterface|ObjectManager $entityManager)
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
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
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
        $locationsLookup = [];
        $itemsLookup = [];
        $actionsLookup = [];

        // Create locations and populate lookup array
        foreach ($locationData as $data) {
            /** @var string */
            $name = $data->getName();
            /** @var string */
            $description = $data->getDescription();
            /** @var string */
            $locationDetails = $data->getDetails();

            $location = new GameLocation($name, $description, $locationDetails);
            $locationsLookup[$data->getId()] = $location;
        }

        // Create items and place them in locations, and populate lookup array
        foreach ($itemData as $data) {
            /** @var string */
            $name = $data->getName();
            /** @var string */
            $description = $data->getDescription();

            $item = new GameItem($name, $description);
            $itemsLookup[$data->getId()] = $item;

            // Place item in location
            $inLocation = $locationsLookup[$data->getLocationId()];
            $inLocation->addItem($item);
        }

        // Create actions and add them to the corresponding items, and populate lookup array
        foreach ($actionData as $data) {
            /** @var string */
            $actionType = $data->getType();

            // Retrieve the corrsponging action class
            $actionClass = $this->getActionClass($actionType);

            if ($actionClass === null) {
                throw new Exception('Action class not found for type: ' . $actionType);
            }

            /**
             * @var ExamineAction|TakeAction|UseAction
             */
            $action = new $actionClass($data->getDescription());

            // Set required location, if applicable
            $locationId = $data->getRequiredLocationId();
            if ($locationId) {
                $requiredLocation = $locationsLookup[$locationId];
                $action->setRequiredLocation($requiredLocation);
            }

            // Add action to item
            $item = $itemsLookup[$data->getItemId()];
            $item->addAction($action);

            $actionsLookup[$data->getId()] = $action;
        }

        // Create responses and add them to the corresponding actions
        foreach ($responseData as $data) {
            /** @var string */
            $responseType = $data->getType();

            // Retrieve the corrsponging response class
            $responseClass = $this->getResponseClass($responseType);

            if ($responseClass === null) {
                throw new Exception('Response class not found for type: ' . $data->getType());
            }

            // Add response location to either swap in or move to when action is triggered
            $location = $locationsLookup[$data->getLocationId()];

            /** @var LocationResponseInterface */
            $response = new $responseClass($location);

            // Add response to action
            $action = $actionsLookup[$data->getActionId()];
            $action->setLocationResponse($response);
        }

        // Create connections between locations
        foreach ($connectionData as $data) {
            $fromLocation = $locationsLookup[$data->getFromLocationId()];
            $toLocation = $locationsLookup[$data->getToLocationId()];

            /** @var string */
            $direction = $data->getDirection();
            $direction = trim(strtolower($direction));

            $fromLocation->connectTo($toLocation, $direction);
        }

        // Create game instance and set starting location
        $game = new Game();
        $game->setCurrentLocation($locationsLookup[1]);

        return $game;
    }
}
