<?php

namespace App\Adventure\ActionResponses;

use App\Adventure\Location;
use App\Adventure\Utils\Connector;

/**
 * ConnectLocationResponse implements the LocationResponseInterface.
 * It connects a new location to the current location in a specified direction.
 */
class ConnectLocationResponse implements LocationResponseInterface
{
    /**
     * @var Location The new location to connect.
     */
    private $locationToConnect;
    
    /**
     * @var string The direction in which to connect the location.
     */
    private $directionToConnect;

    /**
     * ConnectLocationResponse constructor.
     *
     * @param Location $location   The new location to connect.
     * @param string   $direction  The direction in which to connect the location.
     */
    public function __construct(Location $location, string $direction)
    {
        $this->locationToConnect = $location;
        $this->directionToConnect = $direction;
    }

    /**
     * Performs the location response by connecting the new location to the current location.
     *
     * @param  Location $location The current location.
     * @return Location The resulting location after the connection, will be the current location.
     */
    public function doLocationResponse(Location $location): Location
    {
        $resultLocation = Connector::connectLocations($location, $this->locationToConnect, $this->directionToConnect);

        return $resultLocation;
    }
}