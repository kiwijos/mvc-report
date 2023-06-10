<?php

namespace App\Adventure\ActionResponses;

use App\Adventure\Location;
use App\Adventure\Utils\Connector;

/**
 * SwapLocationResponse implements the LocationResponseInterface.
 * It swaps the current location with a new location.
 */
class SwapLocationResponse implements LocationResponseInterface
{
    /**
     * @var Location The new location to swap with the current location.
     */
    private $newLocation;

    /**
     * SwapLocationResponse constructor.
     *
     * @param Location $newLocation The new location to swap with the current location.
     */
    public function __construct(Location $newLocation)
    {
        $this->newLocation = $newLocation;
    }

    /**
     * Performs the location response action by swapping the current location with the new location.
     *
     * @param  Location $oldLocation The current location.
     * @return Location The resulting location after the swap, will be the new location.
     */
    public function doLocationResponse(Location $oldLocation): Location
    {
        $resultLocation = Connector::swapLocations($oldLocation, $this->newLocation);

        return $resultLocation;
    }
}