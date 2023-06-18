<?php

namespace App\Adventure\ActionResponses;

use App\Adventure\Location;

/**
 * MoveLocationResponse implements the LocationResponseInterface.
 * It moves the player to a new location.
 */
class MoveLocationResponse implements LocationResponseInterface
{
    /**
     * @var Location The new location to move to.
     */
    private $newLocation;

    /**
     * MoveLocationResponse constructor.
     *
     * @param Location $newLocation The new location to move to.
     */
    public function __construct(Location $newLocation)
    {
        $this->newLocation = $newLocation;
    }

    /**
     * Performs the location response action by returning the new location.
     *
     * @param  Location|null $oldLocation The current location.
     * @return Location The resulting location after the move, will be the new location.
     */
    public function doLocationResponse(?Location $oldLocation): Location
    {
        $resultLocation = $this->newLocation;

        return $resultLocation;
    }
}
