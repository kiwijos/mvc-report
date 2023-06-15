<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(
    name: 'app:reset-database',
    description: 'Reset the database and import data from CSV files.',
    hidden: false,
    aliases: ['app:reset-game-database']
)]
class ResetGameDatabaseCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:reset-database')
            ->setHelp('This command allows you to reset the game dataabase and import all CSV files.')
            ->setDescription('Reset the database and import data from CSV files.');
    }

    /**
     * Executes the command to reset the database and import data from the CSV files.
     *
     * @param InputInterface  $input  The input interface.
     * @param OutputInterface $output The output interface.
     *
     * @return int The command exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Drop the database schema
        $io->section('Dropping the database schema...');
        $this->runCommand($output, 'doctrine:schema:drop', ['--em' => 'game', '--force' => true]);

        // Update the database schema
        $io->section('Updating the database schema...');
        $this->runCommand($output, 'doctrine:schema:update', ['--em' => 'game', '--force' => true, '--complete' => true]);

        // Import CSV files
        $io->section('Importing CSV files...');
        $this->importCsv($output, 'location.csv', 'game');
        $this->importCsv($output, 'connection.csv', 'game');
        $this->importCsv($output, 'item.csv', 'game');
        $this->importCsv($output, 'action.csv', 'game');
        $this->importCsv($output, 'response.csv', 'game');

        $io->success('Database reset and data import completed.');

        return Command::SUCCESS;
    }

    /**
     * Runs a Symfony console command.
     *
     * @param OutputInterface $output    The output interface for command output
     * @param string          $command   The command name
     * @param array           $arguments The command arguments
     * @param int             $verbosity The desired verbosity level for command output. Default it normal.
     */
    private function runCommand(OutputInterface $output, string $command, array $arguments = [], int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        $application = $this->getApplication();
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => $command,
            ...$arguments,
        ]);

        $output->setVerbosity($verbosity); // Set the desired verbosity level

        $application->run($input, $output);
    }

    /**
     * Imports data from a CSV file using the custom import command.
     *
     * @param OutputInterface $output             The output interface for command output
     * @param string          $csvFile            The path to the CSV file
     * @param string          $entityManagerName  The name of the entity manager
     */
    private function importCsv(OutputInterface $output, string $csvFile, string $entityManagerName): void
    {
        $this->runCommand($output, 'app:import-csv', [
            'filename'     => $csvFile,
            '--manager' => $entityManagerName,
        ],
            OutputInterface::VERBOSITY_QUIET
        );
    }
}