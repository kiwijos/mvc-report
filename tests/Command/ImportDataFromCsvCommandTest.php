<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ImportDataFromCsvCommandTest extends KernelTestCase
{
    /**
     * Test execute function is successfull.
     */
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:import-csv');
        $commandTester = new CommandTester($command);

        // Pass arguments to the helper
        $commandTester->execute([
            'filename' => 'action.csv',
            '--manager' => 'game',
        ]);

        $commandTester->assertCommandIsSuccessful();

        // Check the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('action.csv', $output);
    }
}
