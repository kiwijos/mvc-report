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
        $connection->executeQuery($platform->getTruncateTableSQL(ucfirst($tableName), true));
    }

    /**
     * Asserts that the specified table is empty.
     *
     * @param EntityManagerInterface $entityManager The entity manager instance.
     * @param string                 $tableName     The name of the table to check.
     */
    private function assertEmptyTable(EntityManagerInterface $entityManager, string $tableName): void
    {
        $entityNamespace = $this->getEntityNameSpace($entityManager, $tableName);
        $query = $entityManager->createQuery("SELECT COUNT(e) FROM {$entityNamespace} e");
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
        $entityNamespace = $this->getEntityNameSpace($entityManager, $tableName);
        $query = $entityManager->createQuery("SELECT COUNT(e) FROM {$entityNamespace} e");
        $count = $query->getSingleScalarResult();

        $this->assertGreaterThan(0, $count, "The '$tableName' table is empty.");
    }

    /**
     * Retrieves the namespace for the given table.
     *
     * @param EntityManagerInterface $entityManager The entity manager instance.
     * @param string                 $tableName     The name associated with the entity to retrieve.
     *
     * @return string|null The namespace if found, or null if not found.
     */
    private function getEntityNameSpace(EntityManagerInterface $entityManager, string $tableName): ?string
    {
        $configuration = $entityManager->getConfiguration();
        $entityNamespaces = $configuration->getEntityNamespaces();
        foreach ($entityNamespaces as $namespace) {
            $entityClass = $namespace . '\\' . ucfirst($tableName); // Assume that the name's first character should be uppercase

            if (class_exists($entityClass)) {
                return $entityClass;
            }
        }

        return null;
    }
}
