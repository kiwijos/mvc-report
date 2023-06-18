<?php

namespace App\Adventure\ActionResponses;

use App\Adventure\Location;

/**
 * The LocationResponseInterface represents an action response that affects the current location in the adventure game.
 * Classes implementing this interface should define the behavior of a location response when triggered.
 */
interface LocationResponseInterface
{
    /**
     * Performs the location response action and returns the resulting location.
     *
     * @param  Location|null $location The current location, or null.
     * @return Location The resulting location after the response action.
     */
    public function doLocationResponse(?Location $location): Location;
}
