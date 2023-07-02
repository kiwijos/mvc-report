<?php

namespace App\Tests\Adventure;

use App\Adventure\Game;
use App\Adventure\Location;
use App\Adventure\Inventory;
use App\Adventure\Item;
use App\Adventure\InputActions\InputActionInterface;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Game class.
 */
class GameTest extends TestCase
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
     * Test case for setCurrentLocation and getCurrentLocation methods.
     */
    public function testSetAndGetCurrentLocation(): void
    {
        $this->game->setCurrentLocation($this->location);
        $this->assertSame($this->location, $this->game->getCurrentLocation());
    }

    /**
     * Test case for setInventory and getInventory methods.
     */
    public function testSetAndGetInventory(): void
    {
        $this->game->setInventory($this->inventory);
        $this->assertSame($this->inventory, $this->game->getInventory());
    }

    /**
     * Test case for procesAction method with invalid action.
     */
    public function testProcessActionWithInvalidAction(): void
    {
        $response = $this->game->processAction('invalid', 'target');
        $this->assertSame(
            "That action is not recognized. Try using the 'help' action for assistance.",
            $response
        );
    }

    /**
     * Test case for processAction method with input action 'where'.
     */
    public function testProcessActionWhere(): void
    {
        // Configure mock
        $this->location
            ->method('getLocationDetails')
            ->willReturn('This is a test location');
        $this->game->setCurrentLocation($this->location);

        // Assert 'where' action returns the current location's description
        $response = $this->game->processAction('where', '');
        $this->assertStringContainsString("This is a test location", $response);
    }

    /**
     * Test case for processAction method with input action 'inventory', no items.
     */
    public function testProcessActionInventoryEmpty(): void
    {
        // Configure mock to return an empty inventory
        $this->inventory
            ->method('lookInInventory')
            ->willReturn([]);
        $this->game->setInventory($this->inventory);

        // Assert correct message is returned
        $response = $this->game->processAction('inventory', '');
        $this->assertSame("You look in your inventory:\n(empty)", $response);
    }

    /**
     * Test case for processAction method with input action 'inventory', with items.
     */
    public function testProcessActionInventoryWithItems(): void
    {
        $item1 = $this->createMock(Item::class);
        $item2 = $this->createMock(Item::class);
        $item1->method('getName')->willReturn('Sword');
        $item2->method('getName')->willReturn('Shield');

        $this->inventory
            ->method('lookInInventory')
            ->willReturn(['Sword', 'Shield']);
        $this->game->setInventory($this->inventory);

        // Asssert message contains both items' names
        $response = $this->game->processAction('inventory', '');
        $expected = "You look in your inventory:\nSword\nShield";
        $this->assertSame($expected, $response);
    }

    public function testProcessActionHelp(): void
    {
        $response = $this->game->processAction('help', '');

        // Assert the response contains information about all available actions
        $this->assertStringContainsString('where', $response);
        $this->assertStringContainsString('inventory', $response);
        $this->assertStringContainsString('help', $response);
        $this->assertStringContainsString('go', $response);
        $this->assertStringContainsString('examine', $response);
        $this->assertStringContainsString('take', $response);
        $this->assertStringContainsString('use', $response);
    }

    /**
     * Test case for processAction with input action 'help', with target.
     * @dataProvider helpWithTargetProvider
     */
    public function testProcessActionHelpWithTarget(string $target, string $expected): void
    {
        $response = $this->game->processAction('help', $target);
        $this->assertStringContainsString($expected, $response);
    }

    /**
     * Provides target actions for help and their expected outputs.
     * As the exact message might change, simply expect the output will contain the name of the target action.
     * 
     * @return string[] The inputs and expected outputs.
     */
    public function helpWithTargetProvider(): array
    {
        return [
            ['where', 'where'],
            ['inventory', 'inventory'],
            ['help', 'help'],
            ['go', 'go'],
            ['examine', 'examine'],
            ['take', 'take'],
            ['use', 'use'],
            ['invalid', 'The invalid action is not recognized'], // Not likely to change
        ];
    }

    /**
     * Test case for processAction with input action 'go' in valid direction.
     */
    public function testProcessActionGoValidDirection(): void
    {
        // Create and configure mock locations
        $location1 = $this->createMock(Location::class);
        $location2 = $this->createMock(Location::class);
        $location1
            ->method('hasConnection')
            ->with('north')
            ->willReturn(true);
        $location1
            ->method('getConnectedLocation')
            ->with('north')
            ->willReturn($location2);
        $location2
            ->method('getLocationDetails')
            ->willReturn('This is the second test location.');
        $this->game->setCurrentLocation($location1);

        // Assert message contains the second location's details,
        // also make sure location actually changes
        $response = $this->game->processAction('go', 'north');
        $this->assertStringContainsString('This is the second test location.', $response);
        $this->assertSame($location2, $this->game->getCurrentLocation());
    }

    /**
     * Test case for processAction with input action 'go' with invalid direction.
     */
    public function testProcessActionGoInvalidDirection(): void
    {
        // Create and configure mock location
        $location1 = $this->createMock(Location::class);
        $location1
            ->method('hasConnection')
            ->with('north')
            ->willReturn(false);
        $this->game->setCurrentLocation($location1);

        // Assert error mesage is returned, also make sure location doesn't change
        $response = $this->game->processAction('go', 'north');
        $this->assertSame('You cannot go that way.', $response);
        $this->assertSame($location1, $this->game->getCurrentLocation());
    }

    /**
     * Provides item related actions.
     * 
     * @return string[] The inputs.
     */
    public function itemActionProvider(): array
    {
        return [['examine'], ['take'], ['use']];
    }

    /**
     * Test case for processAction method with no target.
     * @dataProvider itemActionProvider
     */
    public function testProcessItemActionEmptyTarget(string $action): void
    {
        // Assert error message is returned
        $response = $this->game->processAction($action, '');
        $this->assertSame("You must specify a target to {$action}.", $response);
    }

    /**
     * Test case for processAction method with item not found.
     * @dataProvider itemActionProvider
     */
    public function testProcessItemActionItemNotFound(string $action): void
    {
        // Configure location and inventory mocks to NOT find the item
        $this->location->expects($this->once())->method('hasItem')->willReturn(false);
        $this->inventory->expects($this->once())->method('hasItem')->willReturn(false);

        $target = 'key';
        $this->game->setCurrentLocation($this->location);
        $this->game->setInventory($this->inventory);

        // Assert error message is returned
        $response = $this->game->processAction($action, $target);
        $this->assertSame("There is no {$target} to {$action}.", $response);
    }

    /**
     * Test case for processAction method with item with no action object.
     * @dataProvider itemActionProvider
     */
    public function testProcessItemActionNoActionObject(string $action): void
    {
        // Create and configure item mock
        $item = $this->createMock(Item::class);
        $item->method('hasAction')->with($action)->willReturn(false); // No action object

        // Configure location mock to find the item
        $this->location->expects($this->once())->method('hasItem')->willReturn(true);
        $this->location->expects($this->once())->method('getItem')->willReturn($item);

        $target = 'key';
        $this->game->setCurrentLocation($this->location);

        // Assert error message is returned
        $response = $this->game->processAction($action, $target);
        $this->assertSame("You cannot {$action} the {$target}.", $response);
    }
}
