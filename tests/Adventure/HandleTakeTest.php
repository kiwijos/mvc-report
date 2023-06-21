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
    private $game;
    private $location;
    private $inventory;

    protected function setUp(): void
    {
        $this->game = new Game();
        $this->location = $this->createMock(Location::class);
        $this->inventory = $this->createMock(Inventory::class);
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
        $this->location->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(true);

        $this->location->expects($this->once())
            ->method('getItem')
            ->with('key')
            ->willReturn($item);

        $this->location->expects($this->once())
            ->method('removeItem')
            ->with($item);

        $this->inventory->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(false); // Item NOT in inventory

        // Expect item to be added exactly once
        $this->inventory->expects($this->once())
            ->method('addItem')
            ->with($item);

        $this->game->setInventory($this->inventory);
        $this->game->setCurrentLocation($this->location);
        
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
        $this->location->method('hasItem')
            ->with('key')
            ->willReturn(true);

        $this->location->method('getItem')
            ->with('key')
            ->willReturn($item);

        // Configure inventory mock to already have the item
        $this->inventory->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(true);
        
        // Expect item to NOT get removed/added
        $this->location->expects($this->never())
            ->method('removeItem');

        $this->inventory->expects($this->never())
            ->method('addItem');

        // Perform action
        $this->game->setCurrentLocation($this->location);
        $this->game->setInventory($this->inventory);
        $response = $this->game->processAction('take', 'key');

        // Assert error message is returned
        $this->assertSame('The key has already been added to your inventory.', $response);
    }
}
