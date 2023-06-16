<?php

namespace App\Adventure;

use App\Adventure\Utils\Unpacker;
use App\Adventure\Utils\Formatter;
use App\Adventure\Utils\AvailableActionsProvider;
use App\Adventure\InputActions\InputActionInterface;
use App\Adventure\InputActions\ExamineAction;
use App\Adventure\InputActions\TakeAction;
use App\Adventure\InputActions\UseAction;
use App\Adventure\ActionResponses\LocationResponseInterface;
use Exception;

/**
 * Contains the core game logic, handling player actions, locations, and inventory.
 */
class Game
{
    /**
     * @var Location The current location of the player.
     */
    private $currentLocation;

    /**
     * @var Inventory The inventory of the player.
     */
    private $inventory;

    /**
     * Sets the current location of the player.
     *
     * @param Location $location The location object representing the current location.
     */
    public function setCurrentLocation(Location $location): void
    {
        $this->currentLocation = $location;
    }

    /**
     * Retrieves the current location of the player.
     *
     * @return Location The current location of the player.
     */
    public function getCurrentLocation(): Location
    {
        return $this->currentLocation;
    }

    /**
     * Sets the inventory of the player.
     *
     * @param Inventory $inventory The inventory object representing the player's inventory.
     */
    public function setInventory(Inventory $inventory): void
    {
        $this->inventory = $inventory;
    }

    /**
     * Processes the player's action and target.
     *
     * @param  string $action The action to be performed.
     * @param  string $target The target of the action.
     * @return string The response message.
     */
    public function processAction(string $action, string $target): string
    {
        // Trim and convert the action and target to lowercase for consistent comparison
        $action = trim(strtolower($action));
        $target = trim(strtolower($target));

        if (!AvailableActionsProvider::isValidAction($action)) {
            return "That action is not recognized. Try using the 'help' action for assistance.";
        }

        switch ($action) {
            case 'where':
                return $this->handleWhereAction();
            case 'inventory':
                return $this->handleInventoryAction();
            case 'help':
                return $this->handleHelpAction($target);
            case 'go':
                return $this->handleGoAction($target);
            default:
                return $this->processItemAction($action, $target); // Additional processing for the item actions
        }
    }

    /**
     * Handles the 'where' action.
     *
     * @return string The response message containing the description of the current location.
     */
    private function handleWhereAction()
    {
        return Unpacker::unpackLocationDescriptions($this->currentLocation);
    }

    /**
     * Handles the 'inventory' action.
     *
     * @return string The response message containing the items in the inventory.
     */
    private function handleInventoryAction()
    {
        $itemStringsInInventory = $this->inventory->lookInInventory();
        
        $textResponse = "You look in your inventory:\n";

        if (empty($itemStringsInInventory)) {
            return $textResponse . "(empty)";
        }

        return $textResponse . implode("\n", $itemStringsInInventory);
    }

    /**
     * Handles the 'help' action.
     *
     * @param  string $target The target action to provide help for.
     * @return string The response message with the all available actions or information about the specified action.
     */
    private function handleHelpAction(string $target = ''): string
    {
        $availableActions = AvailableActionsProvider::getAvailableActions();

        if (empty($target)) {
            $formattedRows = Formatter::formatRows($availableActions, 4);

            return $formattedRows;
        }

        foreach ($availableActions as $action) {
            if ($target === $action['name']) {
                $formattedRow = Formatter::formatSingleRow($action, 4);

                return $formattedRow;
            }
        }

        return "The {$target} action is not recognized. Try using the 'help' action to see all available actions.";
    }

    /**
     * Handles the 'go' action.
     *
     * @param  string $target The target direction to go.
     * @return string The response message with the new location description or an error message.
     */
    private function handleGoAction(string $direction): string
    {
        $currentLocation = $this->currentLocation;

        if ($currentLocation->hasConnection($direction)) {
            $newLocation = $this->currentLocation->getConnectedLocation($direction);

            $this->currentLocation = $newLocation;

            return Unpacker::unpackLocationDescriptions($newLocation);
        }

        return "You cannot go that way.";
    }

    /**
     * Processes the item action.
     *
     * @param  string $action The action to perform on the item.
     * @param  string $target The target item to perform the action on.
     * @return string The response message based on the action and target.
     */
    private function processItemAction(string $action, string $target): string
    {
        if (empty($target)) {
            return "You must specify a target to {$action}.";
        }
        
        $itemObject = $this->getItemToHandleAction($target);

        if ($itemObject === null) {
            return "There is no {$target} to {$action}.";
        }

        if (!$itemObject->hasAction($action)) {
            return "You cannot {$action} the {$target}.";
        }

        $actionObject = $itemObject->getAction($action);

        switch ($action) {
            case 'examine':
                return $this->handleExamineAction($itemObject, $actionObject);
            case 'take':
                return $this->handleTakeAction($itemObject, $actionObject);
            case 'use':
                return $this->handleUseAction($itemObject, $actionObject);
            default:
                return "The {$target} action is not recognized. Try using the 'help' action for assistance.";
        }
    }

    /**
     * Handles the 'examine' action for an item.
     *
     * @param Item          $itemObject   The item to examine.
     * @param ExamineAction $actionObject The action object associated with the action.
     * @return string The response message from the examine action.
     */
    private function handleExamineAction(Item $itemObject, ExamineAction $actionObject): string
    {
        $textResponse = $actionObject->getTextResponse();

        if (!$textResponse) {
            $itemName = $itemObject->getName();

            return "Upon examining the {$itemName}, you find nothing noteworthy. It appears to be just an ordinary {$itemName}.";
        }

        return $textResponse; 
    }

    /**
     * Handles the 'take' action for an item.
     *
     * @param Item       $itemObject   The item to take.
     * @param TakeAction $actionObject The action object associated with the action.
     * @return string The response message from the take action or an error message.
     */
    private function handleTakeAction(Item $itemObject, TakeAction $actionObject): string
    {
        $itemName = $itemObject->getName();

        // Make sure the item has not been added to the inventory already before taking it
        if ($this->inventory->hasItem($itemName)) {
            return "The {$itemName} has already been added to your inventory.";
        }

        $itemObject->setHidden(true);
        $this->inventory->addItem($itemObject);

        $textResponse = $actionObject->getTextResponse();

        return $textResponse ?? "The {$itemName} has been added to your inventory.";
    }

    /**
     * Handles the 'use' action for an item.
     *
     * @param Item      $itemObject   The item to use.
     * @param UseAction $actionObject The action object associated with the action.
     * @return string The response message from the use action or an error message.
     */
    private function handleUseAction(Item $itemObject, UseAction $actionObject): string
    {
        $itemName = $itemObject->getName();

        // Make sure the item is in the inventory before attempting to use it
        if (!$this->inventory->hasItem($itemName)) {
            return "Make sure you have the {$itemName} before attempting to use it.";
        }

        if (!$this->isCurrentLocationValidForAction($actionObject)) {
            return "You hold the {$itemName} in your hands, eager to use it. But alas, this is not the place.";
        }

        $newLocationDescription = $this->performLocationResponse($actionObject);

        $this->inventory->removeItem($itemObject);

        $textResponse = $actionObject->getTextResponse();

        return $textResponse . "\n\n" . $newLocationDescription;
    }

    /**
     * Checks if the current location is valid for the given action.
     *
     * @param InputActionInterface $actionObject The action object associated with the action.
     * @return bool Whether the current location is valid for the action.
     */
    private function isCurrentLocationValidForAction(InputActionInterface $actionObject): bool
    {
        $requiredLocation = $actionObject->getRequiredLocation();

        if ($requiredLocation === null) {
            return true; // No location requirement means all locations are valid
        }

        return $requiredLocation === $this->currentLocation;
    }

    /**
     * Tries to find the item in the current location or inventory and return it.
     *
     * @param string $target The target item.
     * @return Item|null The item object if found, null otherwise.
     */
    private function getItemToHandleAction(string $target)
    {
        if ($this->currentLocation->hasItem($target)) {
            $itemInLocation = $this->currentLocation->getItem($target);
            
            if (!$itemInLocation->isHidden()) {
                return $itemInLocation;
            }
        }

        if ($this->inventory->hasItem($target)) {
            return $this->inventory->getItem($target);
        }

        return null;
    }

    /**
     * Perform the location response action associated with the given action object.
     *
     * @param InputActionInterface $actionObject The action object containing the location response.
     * @return string The descriptions of connections and items in the resulting location.
     */
    private function performLocationResponse(InputActionInterface $actionObject): string
    {
        $locationResponseObject = $actionObject->getLocationResponse();

        // Execute the location response action and update the current location
        $resultLocation = $locationResponseObject->doLocationResponse($this->currentLocation);
        $this->currentLocation = $resultLocation;

        $resultLocationDescription = Unpacker::unpackLocationDescriptions($resultLocation);
        
        return $resultLocationDescription;
    }
}
