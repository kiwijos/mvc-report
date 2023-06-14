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
            $connectionDescriptions .= "To the {$direction}, {$location->getDescription()}\n";
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
                $itemDescription = self::encloseWordInBrackets($item->getDescription(), $item->getName());
                $itemDescriptions .= $itemDescription . "\n";
            }
        }

        return $itemDescriptions;
    }

    /**
     * Add brackets around a specific word in a given text.
     *
     * @param string $text The input text.
     * @param string $word The word to be enclosed in brackets.
     * @return string The modified text with the word enclosed by brackets.
     */
    private static function encloseWordInBrackets(string $text, string $word): string
    {
        // Escape any special characters in the word
        $escapedWord = preg_quote($word);

        // Create the regex pattern to search for the word
        $pattern = '/\b' . $escapedWord . '\b/i';

        // Add brackets around the word
        $modifiedText = preg_replace($pattern, '[$0]', $text);

        return $modifiedText;
    }
}
