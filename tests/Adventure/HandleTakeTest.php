<?php

namespace App\Tests\Adventure;

use App\Adventure\Game;
use App\Adventure\Location;
use App\Adventure\Inventory;
use App\Adventure\Item;
use App\Adventure\InputActions\TakeAction;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Game class. Specifically testing 'take' action.
 */
class HandleTakeTest extends TestCase
{
    /**
     * @var Game
     */
    private $game;

    protected function setUp(): void
    {
        $this->game = new Game();
    }

    /**
     * Test case for processAction method with input action 'take'.
     */
    public function testProcessActionTakeItem(): void
    {
        // Create and configure take action mock
        $action = $this->createMock(TakeAction::class);
        $action->method('getTextResponse')->willReturn('You take the key.');

        // Create and configure item mock
        $item = $this->createMock(Item::class);
        $item->method('hasAction')->with('take')->willReturn(true);
        $item->method('getAction')->with('take')->willReturn($action);
        $item->method('getName')->willReturn('key');

        // Configure location mock to find the item
        $location = $this->createMock(Location::class);
        $location->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(true);

        $location->expects($this->once())
            ->method('getItem')
            ->with('key')
            ->willReturn($item);

        $location->expects($this->once())
            ->method('removeItem')
            ->with($item);

        $inventory = $this->createMock(Inventory::class);
        $inventory->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(false); // Item NOT in inventory

        // Expect item to be added exactly once
        $inventory->expects($this->once())
            ->method('addItem')
            ->with($item);

        $this->game->setInventory($inventory);
        $this->game->setCurrentLocation($location);

        // Assert that the same take message is returned
        $response = $this->game->processAction('take', 'key');
        $this->assertSame("You take the key.", $response);
    }

    /**
     * Test case for processAction method with 'take' with item already in inventory.
     */
    public function testProcessActionTakeItemAlreadyInInventory(): void
    {
        // Create and configure take action mock
        $action = $this->createMock(TakeAction::class);
        $action->method('getTextResponse')->willReturn('You take the key.');

        // Create and configure item mock
        $item = $this->createMock(Item::class);
        $item->method('hasAction')->with('take')->willReturn(true);
        $item->method('getAction')->with('take')->willReturn($action);
        $item->method('getName')->willReturn('key');

        // Configure location mock to find the item
        $location = $this->createMock(Location::class);
        $location->method('hasItem')
            ->with('key')
            ->willReturn(true);

        $location->method('getItem')
            ->with('key')
            ->willReturn($item);

        // Configure inventory mock to already have the item
        $inventory = $this->createMock(Inventory::class);
        $inventory->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(true);

        // Expect item to NOT get removed/added
        $location->expects($this->never())
            ->method('removeItem');

        $inventory->expects($this->never())
            ->method('addItem');

        // Perform action
        $this->game->setCurrentLocation($location);
        $this->game->setInventory($inventory);
        $response = $this->game->processAction('take', 'key');

        // Assert error message is returned
        $this->assertSame('The key has already been added to your inventory.', $response);
    }
}
