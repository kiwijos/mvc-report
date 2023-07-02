<?php

use App\Adventure\Log;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Log class.
 */
class LogTest extends TestCase
{
    /**
     * Test case for addEntry and getEntriesMethods.
     */
    public function testAddEntryAndGetEntries(): void
    {
        $log = new Log();

        // Add a single entry
        $log->addEntry('Entry 1');

        // Assert the entry is added
        $this->assertEquals(['Entry 1'], $log->getEntries());

        // Add another entry
        $log->addEntry('Entry 2');

        // Assert both entries are added
        $this->assertEquals(['Entry 1', 'Entry 2'], $log->getEntries());
    }

    /**
     * Test case fot addBulkEntry method.
     */
    public function testAddBulkEntry(): void
    {
        $log = new Log();

        // Add multiple entries using bulk string
        $bulkEntry = "Entry 1\nEntry 2\nEntry 3";
        $log->addBulkEntry($bulkEntry);

        // Assert all entries are added
        $this->assertEquals(['Entry 1', 'Entry 2', 'Entry 3'], $log->getEntries());
    }

    /**
     * Test case for dump method.
     */
    public function testDump(): void
    {
        $log = new Log();

        // Add entries including empty lines
        $log->addEntry('Entry 1');
        $log->addEntry('');
        $log->addEntry('Entry 2');
        $log->addEntry('   '); // Empty line with spaces
        $log->addEntry('Entry 3');

        $dumpedLog = $log->dump(); // Dump the log

        // Assert the dumped log string
        $expectedLog = "Entry 1\nEntry 2\nEntry 3";
        $this->assertEquals($expectedLog, $dumpedLog);
    }
}
