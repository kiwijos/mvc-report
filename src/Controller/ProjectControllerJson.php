<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Game\Location;
use App\Repository\Game\LocationRepository;
use App\Entity\Game\Item;
use App\Repository\Game\ItemRepository;
use App\Repository\Game\ConnectionRepository;
use App\Entity\Game\Connection;
use App\Adventure\Log;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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
        $fromId = $request->request->get('from_location');
        $toId = $request->request->get('to_location');
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
}
