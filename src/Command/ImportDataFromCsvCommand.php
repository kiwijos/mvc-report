<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use InvalidArgumentException;

#[AsCommand(
    name: 'app:import-csv',
    description: 'Import data from a CSV file into the database.',
    hidden: false,
    aliases: ['app:import-data', 'app:import-csv-file']
)]
class ImportDataFromCsvCommand extends Command
{
    /**
     * @var EntityManagerInterface|ObjectManager
    */
    private $entityManager;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * ImportDataCommand constructor.
     *
     * @param ManagerRegistry $doctrine
     * @param KernelInterface $kernel
     */
    public function __construct(ManagerRegistry $doctrine, KernelInterface $kernel)
    {
        $this->entityManager = $doctrine->getManager();
        $this->doctrine = $doctrine;
        $this->kernel = $kernel;
        parent::__construct();
    }

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        $this
            ->setName('app:import-data')
            ->setHelp('This command allows you to import data from a CSV file into the database')
            ->setDescription('Import data from a CSV file into the database.')
            ->addArgument('filename', InputArgument::REQUIRED, 'The name of the CSV file (without the directory).')
            ->addOption('manager', 'm', InputOption::VALUE_REQUIRED, 'The name of the EntityManager service to use.', 'default');
    }

    /**
     * Executes the command to import data from the CSV file.
     *
     * @param InputInterface  $input  The input interface.
     * @param OutputInterface $output The output interface.
     *
     * @return int The command exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        /** @var string */
        $filename = $input->getArgument('filename'); // Retrieve the argument value

        /** @var string */
        $csvDirectory = $this->kernel->getProjectDir() . '/' . 'public/csv/';
        $csvFile = $csvDirectory . $filename;

        // Check if file exists
        if (!file_exists($csvFile)) {
            $io->error(sprintf('The CSV file "%s" does not exist.', $csvFile));
            return Command::FAILURE;
        }

        // Provide feedback to user
        $output->writeln([
            'Importing CSV',
            '=============',
            'Filename: ' . $filename,
            'CSV File: ' . $csvFile
        ]);

        // Parse CSV file into array of fields
        /** @var string[] */
        $fileAsArray = file($csvFile);
        $csvData = array_map('str_getcsv', $fileAsArray);

        // Shift off first value of the array, i.e. the headers
        /** @var string[] */
        $headers = array_shift($csvData);

        /** @var string */
        $entityManagerName = $input->getOption('manager');

        try {
            $entityManager = $this->doctrine->getManager($entityManagerName);
        } catch (InvalidArgumentException) {
            $io->error(sprintf('The Entity Manager "%s" does not exist.', $entityManagerName));
            return Command::FAILURE;
        }

        $this->entityManager = $entityManager;

        $output->writeln('Entity manager: ' . $entityManagerName);

        // Retrieve entity based on filename, e.g. location.csv will look for Location entity
        $entityClass = $this->getEntityClass($filename);

        if ($entityClass === null) {
            $io->error(sprintf('No entity class found for the CSV file "%s".', $filename));
            return Command::FAILURE;
        }

        $output->writeln(sprintf("Entity class '%s' found for file '%s'.", $entityClass, $filename));

        foreach ($csvData as $row) {
            $entity = new $entityClass();

            foreach ($row as $key => $value) {
                $property = $headers[$key];
                $setter = 'set' . ucfirst($property);

                if (method_exists($entity, $setter)) {
                    $value = ($value !== '') ? $value : null; // Convert empty strings to null
                    $entity->$setter($value);
                }
            }

            $entityManager->persist($entity);
        }

        $entityManager->flush();

        $io->success('Data imported successfully.');

        return Command::SUCCESS;
    }

    /**
     * Gets the entity class based on the provided CSV file name.
     *
     * @param string $filename The name of the CSV file.
     *
     * @return string|null The entity class name, or null if not found.
     */
    private function getEntityClass(string $filename): ?string
    {
        $entityName = basename($filename, '.csv');

        // Get the entity namespace based on the entity manager configuration
        /** @var EntityManagerInterface */
        $entityManager = $this->entityManager;
        $configuration = $entityManager->getConfiguration();
        $entityNamespaces = $configuration->getEntityNamespaces();
        foreach ($entityNamespaces as $namespace) {
            $entityClass = $namespace . '\\' . ucfirst($entityName);

            if (class_exists($entityClass)) {
                return $entityClass;
            }
        }

        return null;
    }
}
