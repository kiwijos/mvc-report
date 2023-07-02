<?php

use PHPUnit\Framework\TestCase;
use App\Adventure\Location;
use App\Adventure\Item;

/**
 * Unit tests for the Location class.
 */
class LocationTest extends TestCase
{
    /**
     * @var Location The main location object used in the tests.
     */
    private $location;

    /**
     * Set up the main location object for the tests.
     */
    protected function setUp(): void
    {
        $name = 'Forest';
        $description = 'A dense forest.';
        $locationDetails = 'This forest is filled with tall trees and lush vegetation.';
        $this->location = new Location($name, $description, $locationDetails);
    }

    /**
     * Test case for the getName method.
     */
    public function testGetName(): void
    {
        $this->assertEquals('Forest', $this->location->getName());
    }

    /**
     * Test case for the getDescription method.
     */
    public function testGetDescription(): void
    {
        $this->assertEquals('A dense forest.', $this->location->getDescription());
    }

    /**
     * Test case for the getLocationDetails method.
     */
    public function testGetLocationDetails(): void
    {
        $this->assertEquals('This forest is filled with tall trees and lush vegetation.', $this->location->getLocationDetails());
    }

    /**
     * Test case for the connectTo method.
     */
    public function testConnectTo(): void
    {
        $location1 = $this->location;
        $location2 = new Location('Cave', 'A dark cave.', 'The cave is damp and cold.');

        $location1->connectTo($location2, 'north');

        $this->assertTrue($location1->hasConnection('north'));
        $this->assertSame($location2, $location1->getConnectedLocation('north'));
        $this->assertNull($location1->getConnectedLocation('south'));
    }

    /**
     * Test case for the connectTo method when direction is already connected to another location.
     */
    public function testConnectToThrowsException(): void
    {
        $location1 = $this->location;
        $location2 = new Location('Cave', 'A dark cave.', 'The cave is damp and cold.');
        $location3 = new Location('Lake', 'A serene lake.', 'A calm lake reflecting the beauty of its surroundings.');

        $location1->connectTo($location2, 'north');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The direction 'north' is already connected to another location.");

        $location1->connectTo($location3, 'north');
    }

    /**
     * Test case for the disconnectFrom method.
     */
    public function testDisconnectFrom(): void
    {
        $location1 = $this->location;
        $location2 = new Location('Cave', 'A dark cave.', 'The cave is damp and cold.');

        $location1->connectTo($location2, 'north');
        $this->assertTrue($location1->hasConnection('north'));

        $location1->disconnectFrom($location2);
        $this->assertFalse($location1->hasConnection('north'));
    }

    /**
     * Test case for the addItem and getItems methods.
     */
    public function testAddItemAndGetItems(): void
    {
        $location = $this->location;
        $this->assertEquals([], $location->getItems());

        $item1 = $this->createStub(Item::class);
        $item1->method('getName')->willReturn('Sword');

        $item2 = $this->createStub(Item::class);
        $item2->method('getName')->willReturn('Shield');

        $location->addItem($item1);
        $location->addItem($item2);

        $this->assertEquals(['Sword' => $item1, 'Shield' => $item2], $location->getItems());
    }

    /**
     * Test case for the addItem and getItem methods.
     */
    public function testAddItemAndGetItem(): void
    {
        $location = $this->location;

        $item1 = $this->createStub(Item::class);
        $item1->method('getName')->willReturn('Sword');

        $item2 = $this->createStub(Item::class);
        $item2->method('getName')->willReturn('Shield');

        $location->addItem($item1);
        $location->addItem($item2);

        $this->assertSame($item1, $location->getItem('Sword'));
        $this->assertSame($item2, $location->getItem('Shield'));
        $this->assertNull($location->getItem('Axe'));
    }

    /**
     * Test case for the hasItem method.
     */
    public function testHasItem(): void
    {
        $location = $this->location;

        $item = $this->createStub(Item::class);
        $item->method('getName')->willReturn('Sword');

        $location->addItem($item);

        $this->assertTrue($location->hasItem('Sword'));
        $this->assertFalse($location->hasItem('Axe'));
    }
}
