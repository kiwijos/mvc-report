<?php

namespace App\Tests\Adventure;

use App\Adventure\Game;
use App\Adventure\Location;
use App\Adventure\Inventory;
use App\Adventure\Item;
use App\Adventure\InputActions\UseAction;
use App\Adventure\ActionResponses\MoveLocationResponse;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Game class. Specifically testing 'use' action.
 */
class HandleUseTest extends TestCase
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
     * Test case for processAction method with 'use' with item in inventory.
     */
    public function testProcessActionUseItemOnlyInInventory(): void
    {
        // Create a mock location to move to
        $responseLocation = $this->createMock(Location::class);
        $responseLocation->expects($this->once())
            ->method('getLocationDetails')
            ->willReturn('A testy test location.');

        // Create and configure a response mock
        $response = $this->createMock(MoveLocationResponse::class);
        $response->expects($this->once())
            ->method('doLocationResponse')
            ->with($this->location)
            ->willReturn($responseLocation);

        // Create and configure use action mock
        $action = $this->createMock(UseAction::class);
        $action->method('getTextResponse')->willReturn('You use the key.');
        $action->method('getLocationResponse')->willReturn($response);
        
        // Create and configure item mock
        $item = $this->createMock(Item::class);
        $item->method('hasAction')->with('use')->willReturn(true);        
        $item->method('getAction')->with('use')->willReturn($action);
        $item->method('getName')->willReturn('key');

        // Configure inventory mock to find the item
        $this->inventory->expects($this->once())
            ->method('getItem')
            ->with('key')
            ->willReturn($item);
 
        $this->inventory->expects($this->exactly(2))
            ->method('hasItem')
            ->with('key')
            ->willReturn(true);

        // Expect item to be removed after use
        $this->inventory->expects($this->once())
            ->method('removeItem')
            ->with($item);
        
        // Perform action
        $this->game->setInventory($this->inventory);
        $this->game->setCurrentLocation($this->location);
        $response = $this->game->processAction('use', 'key');

        // Assert use message is returned with the resuling locations details
        $this->assertSame("You use the key.\n\nA change has occurred. Behold, the updated location awaits:\nA testy test location.\n", $response);

        // Also, assert location is changed
        $this->assertSame($responseLocation, $this->game->getCurrentLocation());
    }

    /**
     * Test case for processAction method with 'use' with item not in inventory.
     */
    public function testProcessActionUseItemNotInInventory(): void
    {
        // Create and configure use action mock
        $action = $this->createMock(UseAction::class);
        $action->method('getTextResponse')->willReturn('You use the key.');
        
        // Create and configure item mock
        $item = $this->createMock(Item::class);
        $item->method('hasAction')->with('use')->willReturn(true);        
        $item->method('getAction')->with('use')->willReturn($action);
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

        // Configure inventory to NOT have the item
        $this->inventory->method('hasItem')
            ->with('key')
            ->willReturn(false);

        // Expect item to NOT get removed
        $this->inventory->expects($this->never())
            ->method('removeItem');

        // Perform action
        $this->game->setCurrentLocation($this->location);
        $this->game->setInventory($this->inventory);
        $response = $this->game->processAction('use', 'key');

        // Assert error message is returned
        $this->assertSame('Make sure you have the key before attempting to use it.', $response);
    }
}
