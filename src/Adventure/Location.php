<?php

namespace App\Adventure;

use App\Adventure\LocationInterface;

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
     * @var array An array of connected locations.
     */
    private $connectedLocations;

    /**
     * Location constructor.
     *
     * @param string $name        The name of the location.
     * @param string $description The description of the location.
     */
    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
        $this->connectedLocations = [];
    }

    /**
     * Connects the location to another location in a specific direction,
     * e.g. south, east, north or west.
     *
     * @param string   $direction The direction of the connection.
     * @param Location $location  The connected location.
     */
    public function connectTo(string $direction, Location $location): void
    {
        $this->connectedLocations[$direction] = $location;
    }

    /**
     * Returns an array of connected locations.
     *
     * @return array The connected locations.
     */
    public function getConnectedLocations(): array
    {
        return $this->connectedLocations;
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
}
