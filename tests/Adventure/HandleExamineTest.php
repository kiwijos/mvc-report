<?php

namespace App\Tests\Adventure;

use App\Adventure\Game;
use App\Adventure\Location;
use App\Adventure\Inventory;
use App\Adventure\Item;
use App\Adventure\InputActions\ExamineAction;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Game class. Specifically testing 'examine' action.
 */
class HandleExamineTest extends TestCase
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
     * Test case for procesAction with 'examine' with item in location.
     */
    public function testProcessActionExamineItemOnlyInLocation(): void
    {
        // Create and configure examine action mock
        $action = $this->createMock(ExamineAction::class);
        $action->method('getTextResponse')->willReturn('You examine the key.');

        // Create and configure item mock
        $item = $this->createMock(Item::class);
        $item->method('hasAction')->with('examine')->willReturn(true);
        $item->method('getAction')->with('examine')->willReturn($action);

        // Configure location mock to find the item
        $this->location->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(true);

        $this->location->expects($this->once())
            ->method('getItem')
            ->with('key')
            ->willReturn($item);
        
        // If the item is found in the location, it will not be looked for in the inventory
        $this->inventory->expects($this->never())
            ->method('hasItem')
            ->with('key');

        // Perform action
        $this->game->setInventory($this->inventory);
        $this->game->setCurrentLocation($this->location);
        $response = $this->game->processAction('examine', 'key');

        // Assert examine message is returned
        $this->assertSame('You examine the key.', $response);
    }

    /**
     * Test case for procesAction with 'examine' with item only in inventory.
     */
    public function testProcessActionExamineItemOnlyInInventory(): void
    {
        // Create and configure examine action mock
        $action = $this->createMock(ExamineAction::class);
        $action->method('getTextResponse')->willReturn('You examine the key.');

        // Create and configure item mock
        $item = $this->createMock(Item::class);
        $item->method('hasAction')->with('examine')->willReturn(true);
        $item->method('getAction')->with('examine')->willReturn($action);

        // Configure location mock to NOT find the item
        $this->location->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(false);

        // Configure inventory mock to find the item
        $this->inventory->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(true);

        $this->inventory->expects($this->once())
            ->method('getItem')
            ->with('key')
            ->willReturn($item);

        // Perform action
        $this->game->setCurrentLocation($this->location);
        $this->game->setInventory($this->inventory);
        $response = $this->game->processAction('examine', 'key');

        // Assert examine message is returned
        $this->assertSame('You examine the key.', $response);
    }

    /**
     * Test case for procesAction with 'examine' with no/empty text response.
     */
    public function testProcessActionExamineItemDefaultResponse(): void
    {
        // Create and configure examine action mock
        $action = $this->createMock(ExamineAction::class);
        $action->method('getTextResponse')->willReturn(''); // Empty response

        // Create and configure item mock
        $item = $this->createMock(Item::class);
        $item->method('hasAction')->with('examine')->willReturn(true);
        $item->method('getAction')->with('examine')->willReturn($action);

        // Configure location mock to find the item
        $this->location->expects($this->once())
            ->method('hasItem')
            ->with('key')
            ->willReturn(true);

        $this->location->expects($this->once())
            ->method('getItem')
            ->with('key')
            ->willReturn($item);

        // Get a different name just to make it obvious that the method is being called
        $item->expects($this->once())
            ->method('getName')
            ->willReturn('pizza');

        // Perform action
        $this->game->setInventory($this->inventory);
        $this->game->setCurrentLocation($this->location);
        $response = $this->game->processAction('examine', 'key');

        // Assert default examine message is returned
        $this->assertSame('Upon examining the pizza, you find nothing noteworthy. It appears to be just an ordinary pizza.', $response);
    }
}
