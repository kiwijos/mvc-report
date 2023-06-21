<?php

namespace App\Tests;

trait DatabaseHelperTrait
{
    /**
     * Truncates the specified table to remove all data.
     *
     * @param object $entityManager The entity manager instance.
     * @param string $tableName     The name of the table to truncate.
     */
    private function truncateTable($entityManager, string $tableName): void
    {
        $connection = $entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();

        // Execute a truncate table SQL statement for the specified table
        $connection->executeQuery($platform->getTruncateTableSQL($tableName, true));
    }

    /**
     * Asserts that the specified table is empty.
     *
     * @param object $entityManager The entity manager instance.
     * @param string $tableName     The name of the table to check.
     */
    private function assertEmptyTable($entityManager, string $tableName): void
    {
        $tableName = ucfirst($tableName); // Assume that the name's first character should be uppercase
        $query = $entityManager->createQuery("SELECT COUNT(e) FROM App\Entity\Game\\{$tableName} e");
        $count = $query->getSingleScalarResult();

        $this->assertSame(0, $count, "The '$tableName' table is not empty.");
    }

    /**
     * Asserts that the specified table is not empty.
     *
     * @param object $entityManager The entity manager instance.
     * @param string $tableName     The name of the table to check.
     */
    private function assertNonEmptyTable($entityManager, string $tableName): void
    {
        $tableName = ucfirst($tableName); // Assume that the name's first character should be uppercase
        $query = $entityManager->createQuery("SELECT COUNT(e) FROM App\Entity\Game\\{$tableName} e");
        $count = $query->getSingleScalarResult();

        $this->assertGreaterThan(0, $count, "The '$tableName' table is empty.");
    }
}
