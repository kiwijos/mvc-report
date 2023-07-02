<?php

use PHPUnit\Framework\TestCase;
use App\Adventure\Item;
use App\Adventure\InputActions\InputActionInterface;

/**
 * Unit tests for Item class.
 */
class ItemTest extends TestCase
{
    /**
     * @var Item The main item object used in the tests.
     */
    private $item;

    /**
     * Set up the main item object for the tests.
     */
    protected function setUp(): void
    {
        $name = 'Sword';
        $description = 'A powerful sword';
        $this->item = new Item($name, $description);
    }

    /**
     * Test case for the getName method.
     */
    public function testGetName(): void
    {
        $this->assertEquals('Sword', $this->item->getName());
    }

    /**
     * Test case for the getDescription method.
     */
    public function testGetDescription(): void
    {
        $this->assertEquals('A powerful sword', $this->item->getDescription());
    }

    /**
     * Test case for the isHidden method.
     */
    public function testIsHidden(): void
    {
        $item = $this->item;
        $this->assertFalse($item->isHidden());

        $item->setHidden(true);
        $this->assertTrue($item->isHidden());
    }

    /**
     * Test case for the addAction method.
     */
    public function testAddAction(): void
    {
        $item = $this->item;
        $action = $this->createMock(InputActionInterface::class);
        $action->method('getName')->willReturn('Take');

        $item->addAction($action);

        $this->assertTrue($item->hasAction('Take'));
        $this->assertSame($action, $item->getAction('Take'));
    }

    /**
     * Test case for the hasAction method.
     */
    public function testHasAction(): void
    {
        $item = $this->item;
        $this->assertFalse($item->hasAction('Take'));

        $action = $this->createMock(InputActionInterface::class);
        $action->method('getName')->willReturn('Take');
        $item->addAction($action);

        $this->assertTrue($item->hasAction('Take'));
    }

    /**
     * Test case for the getAction method.
     */
    public function testGetAction(): void
    {
        $item = $this->item;
        $this->assertNull($item->getAction('Take'));

        $action = $this->createMock(InputActionInterface::class);
        $action->method('getName')->willReturn('Take');
        $item->addAction($action);

        $this->assertSame($action, $item->getAction('Take'));
    }
}
