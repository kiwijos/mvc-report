<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Command\Command;
use App\Tests\DatabaseHelperTrait;

class ImportDataFromCsvCommandTest extends KernelTestCase
{
    use DatabaseHelperTrait;

    protected function setUp(): void
    {
        // Bootstrap the Symfony kernel with the 'test' environment
        self::bootKernel(['environment' => 'test']);

        // Truncate the location table before each test
        $this->truncateTable($this->getEntityManager('game'), 'location');
    }

    public function testExecute()
    {
        // Get the entity manager for the 'game' connection
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

    public function testExecuteWithNonExistentCsvFile()
    {
        $application = new Application(self::$kernel);

        $command = $application->find('app:import-csv');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'filename' => 'nonexistent.csv',
            '--manager' => 'game',
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        // Check the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('The CSV file "public/csv/nonexistent.csv" does not exist.', $output);
    }

    public function testExecuteWithNonExistentEntityManager()
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

    private function getEntityManager(string $name = 'default')
    {
        $container = self::$kernel->getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager($name);

        return $entityManager;
    }
}
