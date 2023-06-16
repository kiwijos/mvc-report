<?php

namespace App\Adventure;

use Exception;

/**
 * Represents a location in the adventure game.
 */
class Location
{
    /**
     * @var string The name of the location.
     */
    private $name;

    /**
     * @var string The description of the location.
     */
    private $description;

    /**
     * @var string The deailed description of the location.
     */
    private $locationDetails;

    /**
     * @var Location[] An array of connected locations.
     */
    private $connectedLocations;

    /**
     * @var Item[] An array of items in the location.
     */
    private $items;

    /**
     * Location constructor.
     *
     * @param string $name            The name of the location.
     * @param string $description     The description of the location.
     * @param string $locationDetails The detailed description of the location.
     */
    public function __construct(string $name, string $description, string $locationDetails)
    {
        $this->name = $name;
        $this->description = $description;
        $this->locationDetails = $locationDetails;
        $this->connectedLocations = [];
        $this->items = [];
    }

    /**
     * Returns the name of the location.
     *
     * @return string The name of the location.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the description of the location.
     *
     * @return string The description of the location.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Returns the detailed description of the location.
     *
     * @return string The detailed description of the location.
     */
    public function getLocationDetails(): string
    {
        return $this->locationDetails;
    }

    /**
     * Returns an array of connected locations.
     *
     * @return Location[] The connected locations.
     */
    public function getConnectedLocations(): array
    {
        return $this->connectedLocations;
    }

    /**
     * Retrieves the connected location in the specified direction.
     *
     * @param string $direction The direction to retrieve the connected location.
     * @return Location|null The connected location object if found, .
     */
    public function getConnectedLocation(string $direction)
    {
        if ($this->hasConnection($direction)) {
            return $this->connectedLocations[$direction];
        }

        return null;
    }

    /**
     * Connects the location to another location in a specific direction,
     * e.g. south, east, north or west.
     *
     * @param Location $location  The location to connect to.
     * @param string   $direction The direction of the connection.
     * @throws Exception If the specified direction is already connected to another location.
     */
    public function connectTo(Location $location, string $direction): void
    {
        if ($this->hasConnection($direction)) {
            throw new Exception("The direction '{$direction}' is already connected to another location.");
        }

        $this->connectedLocations[$direction] = $location;
    }

    /**
     * Disconnects the given location.
     *
     * @param Location $location The location to disconnect.
     */
    public function disconnectFrom(Location $location): void
    {
        foreach ($this->connectedLocations as $direction => $connectedLocation) {
            if ($connectedLocation === $location) {
                unset($this->connectedLocations[$direction]);
                return;
            }
        }
    }

    /**
     * Checks whether the location is connected to another location
     * in the specified direction.
     * 
     * @param string $direction The direction to check.
     * @return bool Whether the location has a connection in the specified direction.
     */
    public function hasConnection($direction)
    {
        return isset($this->connectedLocations[$direction]);
    }

    /**
     * Returns an array of all items in the location.
     *
     * @return array The items in the location.
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Retrieves the item with the specified name.
     *
     * @param  string $itemName The name of the item.
     * @return Item|null The item object if found, or null if not found.
     */
    public function getItem(string $itemName): ?Item
    {
        if ($this->hasItem($itemName)) {
            return $this->items[$itemName];
        }

        return null;
    }

    /**
     * Checks whether the given item is in the location.
     * 
     * @param string $itemName The name of the item.
     * @return bool Whether the item is in the location.
     */
    public function hasItem(string $itemName): bool
    {
        return isset($this->items[$itemName]);
    }

    /**
     * Adds the item to the location.
     * 
     * @param Item $item The item to add to the location.
     */
    public function addItem(Item $item): void
    {
        $this->items[$item->getName()] = $item;
    }
}
