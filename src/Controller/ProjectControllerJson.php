<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Adventure\Log;
use App\Entity\Game\Connection;
use App\Entity\Game\Item;
use App\Entity\Game\Location;
use App\Repository\Game\ConnectionRepository;
use App\Repository\Game\ItemRepository;
use App\Repository\Game\LocationRepository;

class ProjectControllerJson extends AbstractController
{
    #[Route("/proj/api/locations", name: "proj_api_locations")]
    public function locations(LocationRepository $locationRepository): Response
    {
        $locations = $locationRepository->findAll();

        if (empty($locations)) {
            return $this->json(['error' => 'No locations found'], Response::HTTP_NOT_FOUND);
        }

        $response = $this->json($locations);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/proj/api/locations/{id}", name: "proj_api_locations_by_id")]
    public function locationById(int $id, LocationRepository $locationRepository): JsonResponse
    {
        $location = $locationRepository->find($id);

        if (!$location) {
            return $this->json(['error' => "No location found with ID {$id}"], Response::HTTP_NOT_FOUND);
        }

        $response = $this->json($location);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/proj/api/items", name: "proj_api_items")]
    public function items(ItemRepository $itemRepository): Response
    {
        $items = $itemRepository->findAll();

        if (empty($items)) {
            return $this->json(['error' => 'No items found'], Response::HTTP_NOT_FOUND);
        }

        $response = $this->json($items);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/proj/api/items/{id}", name: "proj_api_items_by_id")]
    public function itemById(int $id, ItemRepository $itemRepository): JsonResponse
    {
        $item = $itemRepository->find($id);

        if (!$item) {
            return $this->json(['error' => "No item found with ID {$id}"], Response::HTTP_NOT_FOUND);
        }

        $response = $this->json($item);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/proj/api/log", name: "proj_api_log")]
    public function log(SessionInterface $session): JsonResponse
    {
        /** @var Log */
        $log = $session->get('game_log', new Log());

        $entries = $log->getEntries();
        if (empty($entries)) {
            return $this->json(['error' => 'No logged entries found'], Response::HTTP_NOT_FOUND);
        }

        $response = $this->json($entries);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT,
        );
        return $response;
    }

    #[Route("/proj/api/connect", name: "proj_api_connect", methods: ['POST'])]
    public function connect(Request $request, ManagerRegistry $doctrine, ConnectionRepository $connectionRepository): JsonResponse
    {
        // Retrieve request data
        /** @var int */
        $fromId = intval($request->request->get('from_location'));
        /** @var int */
        $toId = intval($request->request->get('to_location'));
        /** @var string */
        $direction = $request->request->get('direction');

        // Exit early with error message if locations are the same
        if ($fromId ===  $toId) {
            return $this->json(['error' => 'Cannot connect a location to itself'], Response::HTTP_BAD_REQUEST);
        }

        // Try to find if connection already exists
        $existingConnection = $connectionRepository->findOneBy([
            'fromLocationId' => $fromId,
            'toLocationId' => $toId,
            'direction' => $direction,
        ]);

        // Exit early with error message if connection already exists
        if ($existingConnection !== null) {
            return $this->json(['error' => 'Locations are already connected in the specified direction'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Find the highest ID among existing connections
            $highestId = $connectionRepository->findHighestId();

            // Create the connection
            $connection = new Connection();
            $connection->setId($highestId + 1);
            $connection->setFromLocationId($fromId);
            $connection->setToLocationId($toId);
            $connection->setDirection($direction);

            // Persist new connection in the database
            $entityManager = $doctrine->getManager('game');
            $entityManager->persist($connection);
            $entityManager->flush();
        } catch (Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = [
            'message' => 'Locations connected successfully',
            'from_location_id' => $fromId,
            'to_location_id' => $toId,
            'direction' => $direction,
        ];

        $response = $this->json($response, Response::HTTP_CREATED);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT,
        );

        return $response;
    }


    /**
     * Reset the game database by running the reset-database command.
     */
    #[Route("/proj/api/reset", name: "proj_api_reset", methods: ['POST'])]
    public function resetDatabase(KernelInterface $kernel): JsonResponse
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $command = 'app:reset-database';

        $input = new ArrayInput([
            'command' => $command,
        ]);

        $output = new NullOutput();

        try {
            if (!$application->has($command)) {
                throw new CommandNotFoundException("The command {$command} does not exist.");
            }

            $statusCode = $application->run($input, $output);

            if ($statusCode === 0) {
                $response = $this->json([
                    'status' => 'success',
                    'message' => 'Database reset data import completed!',
                ], Response::HTTP_OK);

                $response->setEncodingOptions(
                    $response->getEncodingOptions() | JSON_PRETTY_PRINT,
                );

                return $response;
            }

            $errorMessage = 'Database reset and data import failed.';
            $errorStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        } catch (CommandNotFoundException $exception) {
            $errorMessage = $exception->getMessage();
            $errorStatusCode = Response::HTTP_NOT_FOUND;
        } catch (Exception $exception) {
            $errorMessage = $exception->getMessage();
            $errorStatusCode = Response::HTTP_BAD_REQUEST;
        }

        $response = $this->json([
            'status' => 'error',
            'message' => $errorMessage,
        ], $errorStatusCode);

        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT,
        );

        return $response;
    }
}
