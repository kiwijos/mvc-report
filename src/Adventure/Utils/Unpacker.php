<?php

namespace App\Adventure\Utils;

use App\Adventure\Location;

/**
 * The Unpacker class is responsible for unpacking location descriptions,
 * that includes descriptions of visible connections and visible items in the location.
 */
class Unpacker
{
    /**
     * Unpacks the location by showing the detailed description,
     * visible connections and visible items in the location.
     *
     * @param Location $location The location to unpack.
     * @return string The unpacked location descriptions.
     */
    public static function unpackLocationDescriptions(Location $location): string
    {
        $locationText = $location->getLocationDetails() . "\n";
        $locationText .= self::unpackVisibleConnectionsToLocation($location);
        $locationText .= self::unpackVisibleItemsInLocation($location);
        
        return $locationText;  
    }

    /**
     * Unpacks the visible connections to the location.
     *
     * @param Location $location The location to unpack connections from.
     * @return string The unpacked connection descriptions.
     */
    public static function unpackVisibleConnectionsToLocation(Location $location): string
    {
        $connectedLocations = $location->getConnectedLocations();

        $connectionDescriptions = "";
        foreach ($connectedLocations as $direction => $location) {
            $connectionDescriptions .= "To the {$direction} you see {$location->getDescription()}\n";
        }

        return $connectionDescriptions;
    }

    /**
     * Unpacks the visible items in the location.
     *
     * @param Location $location The location to unpack items from.
     * @return string The unpacked item descriptions.
     */
    public static function unpackVisibleItemsInLocation(Location $location): string
    {
        $itemsInLocation = $location->getItems();

        $itemDescriptions = "";
        foreach ($itemsInLocation as $item) {
            if (!$item->isHidden()) {
                $itemDescriptions .= "{$item->getDescription()}\n";
            }
        }

        return $itemDescriptions;
    }
}
