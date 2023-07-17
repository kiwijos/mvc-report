<?php

namespace App\Tests\Command;

use App\Tests\DatabaseHelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ImportDataFromCsvCommandTest extends KernelTestCase
{
    use DatabaseHelperTrait;

    /**
     * Bootstrap kernel and prepare database.
     */
    protected function setUp(): void
    {
        // Bootstrap the Symfony kernel with the 'test' environment
        self::bootKernel(['environment' => 'test']);

        /** @var EntityManagerInterface */
        $entityManager = $this->getEntityManager('game');

        // Truncate the location table before each test
        $this->truncateTable($entityManager, 'location');
    }

    /**
     * Test case for execute method with valid input.
     */
    public function testExecute(): void
    {
        // Get the entity manager for the 'game' connection
        /** @var EntityManagerInterface */
        $entityManager = $this->getEntityManager('game');

        // Check the contents of the 'location' table before executing the command
        $this->assertEmptyTable($entityManager, 'location');

        $application = new Application(self::$kernel);

        $command = $application->find('app:import-csv');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'filename' => 'location.csv',
            '--manager' => 'game',
        ]);

        $commandTester->assertCommandIsSuccessful();

        // Check the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString("Importing CSV\n=============\nFilename: location.csv", $output);

        // Check the contents of the 'location' table after executing the command
        $this->assertNonEmptyTable($entityManager, 'location');
    }

    /**
     * Test case for execute method with invalid file.
     */
    public function testExecuteWithNonExistentCsvFile(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('app:import-csv');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'filename' => 'nonexistent.csv',
            '--manager' => 'game',
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        // Expected path to the file
        $csvPath = self::$kernel->getProjectDir() . '/public/csv/nonexistent.csv';

        // Check the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('The CSV file "' . $csvPath . '" does not exist.', $output);
    }

    /**
     * Test case for execute method with invalid entity manager.
     */
    public function testExecuteWithNonExistentEntityManager(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('app:import-csv');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'filename' => 'location.csv',
            '--manager' => 'nonexistent',
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        // Check the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('The Entity Manager "nonexistent" does not exist.', $output);
    }

    /**
     * Test case for execute method with missing entity class.
     */
    public function testExecuteWithNoEntityClassFound(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find('app:import-csv');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'filename' => 'dummy.csv',
            '--manager' => 'game',
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        // Check the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('No entity class found for the CSV file "dummy.csv".', $output);
    }

    /**
     * @return object The entity manager.
     */
    private function getEntityManager(string $name = 'default')
    {
        $container = self::$kernel->getContainer();

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');

        $entityManager = $doctrine->getManager($name);

        return $entityManager;
    }
}
