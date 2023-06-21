<?php

use PHPUnit\Framework\TestCase;
use App\Adventure\Inventory;
use App\Adventure\Item;

/**
 * Unit tests for the Inventory class.
 */
class InventoryTest extends TestCase
{
    /**
     * Test case for the getInventory method.
     */
    public function testGetInventory()
    {
        $inventory = new Inventory();
        $this->assertEquals([], $inventory->getInventory());

        $item1 = $this->createStub(Item::class);
        $item1->method('getName')->willReturn('Sword');

        $item2 = $this->createStub(Item::class);
        $item2->method('getName')->willReturn('Shield');

        $inventory->addItem($item1);
        $inventory->addItem($item2);

        $this->assertEquals(['Sword' => $item1, 'Shield' => $item2], $inventory->getInventory());
    }

    /**
     * Test case for the lookInInventory method.
     */
    public function testLookInInventory()
    {
        $inventory = new Inventory();
        $this->assertEquals([], $inventory->lookInInventory());

        $item1 = $this->createStub(Item::class);
        $item1->method('getName')->willReturn('Sword');

        $item2 = $this->createStub(Item::class);
        $item2->method('getName')->willReturn('Shield');

        $inventory->addItem($item1);
        $inventory->addItem($item2);

        $this->assertEquals(['Sword', 'Shield'], $inventory->lookInInventory());
    }

    /**
     * Test case for the getItem method.
     */
    public function testGetItem()
    {
        $inventory = new Inventory();
        $item = $this->createStub(Item::class);
        $item->method('getName')->willReturn('Sword');
        $inventory->addItem($item);

        $this->assertSame($item, $inventory->getItem('Sword'));
        $this->assertNull($inventory->getItem('Shield'));
    }

    /**
     * Test case for the hasItem method.
     */
    public function testHasItem()
    {
        $inventory = new Inventory();
        $item = $this->createStub(Item::class);
        $item->method('getName')->willReturn('Sword');
        $inventory->addItem($item);

        $this->assertTrue($inventory->hasItem('Sword'));
        $this->assertFalse($inventory->hasItem('Shield'));
    }

    /**
     * Test case for the addItem method.
     */
    public function testAddItem()
    {
        $inventory = new Inventory();
        $item = $this->createMock(Item::class);
        $item->expects($this->once())
            ->method('getName')
            ->willReturn('Sword');

        $inventory->addItem($item);

        $this->assertTrue($inventory->hasItem('Sword'));
        $this->assertSame($item, $inventory->getItem('Sword'));
    }

    /**
     * Test case for the removeItem method.
     */
    public function testRemoveItem()
    {
        $inventory = new Inventory();
        $item = $this->createStub(Item::class);
        $item->method('getName')->willReturn('Sword');
        $inventory->addItem($item);

        $this->assertTrue($inventory->hasItem('Sword'));

        $inventory->removeItem($item);
        $this->assertFalse($inventory->hasItem('Sword'));
        $this->assertNull($inventory->getItem('Sword'));
    }
}
