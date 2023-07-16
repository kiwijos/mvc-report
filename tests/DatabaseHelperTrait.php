<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;

trait DatabaseHelperTrait
{
    /**
     * Truncates the specified table to remove all data.
     *
     * @param EntityManagerInterface $entityManager The entity manager instance.
     * @param string                 $tableName     The name of the table to truncate.
     */
    private function truncateTable(EntityManagerInterface $entityManager, string $tableName): void
    {
        $connection = $entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();

        // Execute a truncate table SQL statement for the specified table
        $connection->executeQuery($platform->getTruncateTableSQL($tableName, true));
    }

    /**
     * Asserts that the specified table is empty.
     *
     * @param EntityManagerInterface $entityManager The entity manager instance.
     * @param string                 $tableName     The name of the table to check.
     */
    private function assertEmptyTable(EntityManagerInterface $entityManager, string $tableName): void
    {
        $tableName = ucfirst($tableName); // Assume that the name's first character should be uppercase
        $query = $entityManager->createQuery("SELECT COUNT(e) FROM App\Entity\Game\\{$tableName} e");
        $count = $query->getSingleScalarResult();

        $this->assertSame(0, $count, "The '$tableName' table is not empty.");
    }

    /**
     * Asserts that the specified table is not empty.
     *
     * @param EntityManagerInterface $entityManager The entity manager instance.
     * @param string                 $tableName     The name of the table to check.
     */
    private function assertNonEmptyTable(EntityManagerInterface $entityManager, string $tableName): void
    {
        $tableName = ucfirst($tableName); // Assume that the name's first character should be uppercase
        $query = $entityManager->createQuery("SELECT COUNT(e) FROM App\Entity\Game\\{$tableName} e");
        $count = $query->getSingleScalarResult();

        $this->assertGreaterThan(0, $count, "The '$tableName' table is empty.");
    }
}
