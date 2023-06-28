<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:reset-database',
    description: 'Reset the database and import data from CSV files.',
    hidden: false,
    aliases: ['app:reset-game-database']
)]
class ResetGameDatabaseCommand extends Command
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        parent::__construct();
    }

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

        $csvFiles = ['location.csv', 'connection.csv', 'item.csv', 'action.csv', 'response.csv'];
        $csvDirectory = $this->kernel->getProjectDir() . '/' . 'public/csv';

        // Make sure files are present before attempting to reset database
        $missingFiles = $this->getMissingCsvFiles($csvFiles, $csvDirectory);
        if (!empty($missingFiles)) {
            $io->error('The following CSV files are missing in the ' . $csvDirectory . ' directory: ' . implode(', ', $missingFiles) . '. Aborting database reset!');
            return Command::FAILURE;
        }

        // Drop the database schema
        $io->section('Dropping the database schema...');
        $this->runCommand($output, 'doctrine:schema:drop', ['--em' => 'game', '--force' => true]);

        // Update the database schema
        $io->section('Updating the database schema...');
        $this->runCommand($output, 'doctrine:schema:update', ['--em' => 'game', '--force' => true, '--complete' => true]);

        // Import CSV files
        $io->section('Importing CSV files...');
        $output->writeln([
            '',
            ' Importing CSV files...',
        ]);

        foreach ($csvFiles as $filename) {
            $this->importCsv($output, $filename, 'game');
        }

        // Create message indicating successful import
        $fileCount = count($csvFiles);
        $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
        $output->writeln([
            '',
            "     <info>{$fileCount}</info> CSV files imported",
            '',
        ]);

        $io->success('Database reset and data import completed!');

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

    /**
     * @param string[] $csvFiles The array of files to see if present.
     * @param string   $csvDirectory The path to the csv files.
     * 
     * @return string[] The missing CSV files.
     */
    private function getMissingCsvFiles(array $csvFiles, string $csvDirectory): array
    {
        $filesystem = new Filesystem();
        $missingFiles = [];
        foreach ($csvFiles as $filename) {
            $filePath = $csvDirectory . '/' . $filename;
            if (!$filesystem->exists($filePath)) {
                $missingFiles[] = $filename;
            }
        }
        return $missingFiles;
    }
}
