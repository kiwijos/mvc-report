<?php

namespace App\Adventure;

/**
 * Represents an adventure log that keeps track of user input.
 */
class Log
{
    /**
     * @var array An array of logged entries.
     */
    private $entries;

    /**
     * Log constructor.
     */
    public function __construct()
    {
        $this->entries = [];
    }

    /**
     * Adds an entry to the log.
     *
     * @param string $entry The entry to be added.
     */
    public function addEntry(string $entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * Retrieves all logged entries.
     *
     * @return array The logged entries.
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}
