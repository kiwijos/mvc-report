<?php

namespace App\Adventure;

/**
 * Represents the player in the adventure game.
 */
class Player
{
    /**
     * @var Location The current location of the player.
     */
    private $currentLocation;

    /**
     * Sets the current location of the player.
     *
     * @param Location $location The location to set as the current location.
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
     * Performs an action based on the provided action and target.
     *
     * @param string $action The action to perform.
     * @param string $target The target of the action.
     */
    public function performAction(string $action, string $target): void
    {
        $action = trim(strtolower($action));
        $target = trim(strtolower($target));

        if ($action === 'go') {
            $currentLocation = $this->getCurrentLocation();
            $connectedLocations = $currentLocation->getConnectedLocations();

            if (array_key_exists($target, $connectedLocations)) {
                $newLocation = $connectedLocations[$target];
                $this->setCurrentLocation($newLocation);
            } else {
                // Handle invalid direction
            }
        } else {
            // Handle other actions
        }
    }
}
