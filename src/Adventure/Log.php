<?php

namespace App\Adventure;

/**
 * Represents an adventure log that keeps track of user input.
 */
class Log
{
    /**
     * @var string[] An array of logged entries.
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
    public function addEntry(string $entry): void
    {
        $this->entries[] = $entry;
    }

    /**
     * Adds multiple entries to the log from a bulk string.
     *
     * @param string $bulkEntry The bulk string containing multiple entries separated by "\n".
     */
    public function addBulkEntry(string $bulkEntry): void
    {
        $trimmedEntry = trim($bulkEntry);
        $lines = explode("\n", $trimmedEntry);

        foreach ($lines as $line) {
            $this->entries[] = $line;
        }
    }

    /**
     * Retrieves all logged entries.
     *
     * @return string[] The logged entries.
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * Dumps the log entries as a string, filtering out empty entries.
     *
     * @return string The log entries as a string.
     */
    public function dump(): string
    {
        $filteredEntries = array_filter($this->entries, function ($entry) {
            return trim($entry) !== '';
        });

        return implode("\n", $filteredEntries);
    }
}
