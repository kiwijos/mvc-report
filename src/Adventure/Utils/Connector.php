<?php

namespace App\Adventure\Utils;

use App\Adventure\Location;
use Exception;

class Connector
{
    /**
     * Connects two locations in a specified direction.
     *
     * @param Location $firstLocation  The first location.
     * @param Location $secondLocation The location to connect.
     * @param string   $direction      The direction in which to connect the locations.
     *
     * @return Location The current location after the connection
     * @throws Exception If the direction is already connected to another location.
     */
    public static function connectLocations(Location $firstLocation, Location $secondLocation, string $direction): Location
    {
        // Check if the direction is already used
        if ($firstLocation->hasConnection($direction)) {
            throw new Exception("The direction '{$direction}' is already connected to another location.");
        }

        // Connect the locations
        $firstLocation->connectTo($secondLocation, $direction);
        $secondLocation->connectTo($firstLocation, self::getOppositeDirection($direction));

        return $firstLocation;
    }

    /**
     * Swaps one location for another and connects the new location to all locations previously connected to the old location.
     *
     * @param Location $oldLocation The location to be replaced.
     * @param Location $newLocation The new location to replace the old location.
     *
     * @return Location The new location.
     * @throws Exception If there is an error in swapping the locations.
     */
    public static function swapLocations(Location $oldLocation, Location $newLocation): Location
    {
        // Get the connections of the old location
        $connections = $oldLocation->getConnectedLocations();

        // Remove the old location from its connections
        foreach ($connections as $location) {
            $location->disconnectFrom($oldLocation);
        }

        // Add the new location to the connections
        foreach ($connections as $direction => $location) {
            self::connectLocations($newLocation, $location, $direction);
        }

        return $newLocation;
    }

    /**
     * Returns the opposite direction for the given direction.
     *
     * @param string $direction The direction for which to find the opposite direction.
     *
     * @return string The opposite direction.
     * @throws Exception If no opposite direction is found for the given direction.
     */
    public static function getOppositeDirection(string $direction): string
    {
        $oppositeDirections = [
            'north' => 'south',
            'south' => 'north',
            'east' => 'west',
            'west' => 'east',
        ];

        // Check if the opposite direction exists
        if (isset($oppositeDirections[$direction])) {
            return $oppositeDirections[$direction];
        }

        throw new Exception("No opposite direction found for '{$direction}'.");
    }
}
