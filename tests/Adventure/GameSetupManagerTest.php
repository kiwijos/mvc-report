<?php

use PHPUnit\Framework\TestCase;
use App\Adventure\Utils\GameSetupManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use App\Entity\Game\Location;
use App\Entity\Game\Item;
use App\Entity\Game\Action;
use App\Entity\Game\Response;
use App\Entity\Game\Connection;
use App\Adventure\Game;
use App\Adventure\Location as AdventureLocation;
use App\Adventure\InputActions\UseAction;
use App\Adventure\InputActions\ExamineAction;
use App\Adventure\ActionResponses\SwapLocationResponse;

/**
 * Unit tests for GameSetupManager class.
 */
class GameSetupManagerTest extends TestCase
{
    /**
     * Test case for setupGameFromDatabase method.
     */
    public function testSetupGameFromDatabase()
    {
        // Mock the manager
        $objectManager = $this->createMock(ObjectManager::class);

        // Set up the mock to return sample data from repositories
        $objectManager->method('getRepository')->willReturnMap([
            [Location::class, $this->getLocationRepositoryMock()],
            [Item::class, $this->getItemRepositoryMock()],
            [Action::class, $this->getActionRepositoryMock()],
            [Response::class, $this->getResponseRepositoryMock()],
            [Connection::class, $this->getConnectionRepositoryMock()],
        ]);

        // Create an instance of GameSetupManager with the mock manager
        $gameSetupManager = new GameSetupManager($objectManager);

        // Configure game object
        $game = $gameSetupManager->setupGameFromDatabase();

        // Assert that the returned object is a Game instance
        $this->assertInstanceOf(Game::class, $game);

        // Perform additional assertion to check if the game object is configured as expected
        $forrest = $game->getCurrentLocation();
        $this->assertInstanceOf(AdventureLocation::class, $forrest);
        $this->assertSame('Forrest', $forrest->getName());

        $this->assertTrue($forrest->hasConnection('north')); // To the Cave
        $this->assertFalse($forrest->hasConnection('south')); // No connection
        $this->assertInstanceOf(AdventureLocation::class, $forrest->getConnectedLocation('north'));
        $this->assertNull($forrest->getConnectedLocation('south'));

        $cave = $forrest->getConnectedLocation('north');
        $this->assertSame('Cave', $cave->getName());

        $this->assertTrue($cave->hasItem('Axe'));
        $this->assertFalse($cave->hasItem('Sword'));
        $this->assertNull($cave->getItem('Sword'));

        $axe = $cave->getItem('Axe');
        $this->assertSame('Axe', $axe->getName());
        $this->assertTrue($axe->hasAction('take'));

        $examineAxe = $axe->getAction('examine');
        $this->assertInstanceOf(ExamineAction::class, $examineAxe);
        $this->assertNull($examineAxe->getRequiredLocation());
        $this->assertNull($examineAxe->getLocationResponse());

        $useAxe = $axe->getAction('use');
        $this->assertInstanceOf(UseAction::class, $useAxe);
        $this->assertSame($forrest, $useAxe->getRequiredLocation());
        $this->assertInstanceOf(SwapLocationResponse::class, $useAxe->getLocationResponse());
    }

    /**
     * @return ObjectRepository The mocked Location repository.
     */
    private function getLocationRepositoryMock()
    {
        $location1 = new Location();
        $location1->setId(1);
        $location1->setName('Forrest')
            ->setDescription('A dense forest.')
            ->setDetails('This forest is filled with tall trees and lush vegetation.');

        $location2 = new Location();
        $location2->setId(2);
        $location2->setName('Cave')
            ->setDescription('A dark cave.')
            ->setDetails('The cave is damp and cold.');

        $location3 = new Location();
        $location3->setId(3);
        $location3->setName('Lake')
            ->setDescription('A serene lake.')
            ->setDetails('A calm lake reflecting the beauty of its surroundings.');

        $locationRepository = $this->createMock(ObjectRepository::class);
        $locationRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                $location1,
                $location2,
                $location3,
            ]);

        return $locationRepository;
    }

    /**
     * @return ObjectRepository The mocked Item repository.
     */
    private function getItemRepositoryMock()
    {
        $item1 = new Item();
        $item1
            ->setId(1)
            ->setName('Sword')
            ->setDescription('A powerful sword.')
            ->setLocationId(1);

        $item2 = new Item();
        $item2
            ->setId(2)
            ->setName('Axe')
            ->setDescription('A heavy axe.')
            ->setLocationId(2);

        $itemRepository = $this->createMock(ObjectRepository::class);
        $itemRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([
                $item1,
                $item2,
            ]);

        return $itemRepository;
    }

    /**
     * @return ObjectRepository The mocked Action repository.
     */
    private function getActionRepositoryMock()
    {
        $action1 = new Action();
        $action1
            ->setId(1)
            ->setType('take')
            ->setItemId(2)
            ->setDescription('You take the axe.')
            ->setRequiredLocationId(null);

        $action2 = new Action();
        $action2
            ->setId(2)
            ->setType('examine')
            ->setItemId(2)
            ->setDescription('You examine the axe.')
            ->setRequiredLocationId(null);

        $action3 = new Action();
        $action3
            ->setId(3)
            ->setType('use')
            ->setItemId(2)
            ->setDescription('You use the axe.')
            ->setRequiredLocationId(1);

        $actionRepository = $this->createMock(ObjectRepository::class);
        $actionRepository->expects($this->once())
            ->method('findAll')
            ->willReturn(
                [
                $action1,
                $action2,
                $action3,
            ]
            );

        return $actionRepository;
    }

    /**
     * @return ObjectRepository The mocked Response repository.
     */
    private function getResponseRepositoryMock()
    {
        $response = new Response();
        $response
            ->setId(1)
            ->setActionId(3)
            ->setType('swap')
            ->setLocationId(3);

        $responseRepository = $this->createMock(ObjectRepository::class);
        $responseRepository->expects($this->once())
            ->method('findAll')
            ->willReturn(
                [
                $response,
            ]
            );

        return $responseRepository;
    }

    /**
     * @return ObjectRepository The mocked Connection repository.
     */
    private function getConnectionRepositoryMock()
    {
        $connection1 = new Connection();
        $connection1
            ->setId(1)
            ->setFromLocationId(1)
            ->setToLocationId(2)
            ->setDirection('north');

        $connection2 = new Connection();
        $connection2
            ->setId(2)
            ->setFromLocationId(2)
            ->setToLocationId(1)
            ->setDirection('south');

        $connectionRepository = $this->createMock(ObjectRepository::class);
        $connectionRepository->expects($this->once())
            ->method('findAll')
            ->willReturn(
                [
                $connection1,
                $connection2,
            ]
            );

        return $connectionRepository;
    }
}
