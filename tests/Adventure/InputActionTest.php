<?php

use PHPUnit\Framework\TestCase;
use App\Adventure\InputActions\InputActionInterface;
use App\Adventure\InputActions\InputActionTrait;
use App\Adventure\ActionResponses\LocationResponseInterface;
use App\Adventure\Location;

/**
 * Represents a test input action.
 * This class implements the InputActionInterface and uses the InputActionTrait
 * to provide the same functionality as the other actions used in the game.
 */
class TestAction implements InputActionInterface
{
    use InputActionTrait;

    /**
     * Retrieves the name that identifies the test input action.
     *
     * @return string The name representing the test input action.
     */
    public function getName(): string
    {
        return 'test';
    }
}

/**
 * Unit tests for input actions.
 * It essentially tests the functionality provided by the InputActionTrait.
 */
class InputActionTest extends TestCase
{
    /**
     * Test case for getName method (not provided by the trait, by the way).
     */
    public function testGetName()
    {
        $action = new TestAction('');
        $this->assertEquals('test', $action->getName());
    }

    /**
     * Test case for getTextResponse method.
     */
    public function testGetTextResponse()
    {
        $action = new TestAction('This is a test response');
        $this->assertEquals('This is a test response', $action->getTextResponse());
    }

    /**
     * Test case for setRequiredLocation and getRequiredLocation methods.
     */
    public function testSetAndGetLocationResponse()
    {
        $action = new TestAction('');
        $this->assertNull($action->getLocationResponse());

        $locationResponse = $this->createStub(LocationResponseInterface::class);
        $action->setLocationResponse($locationResponse);

        $this->assertSame($locationResponse, $action->getLocationResponse());
    }

    /**
     * Test case for setRequiredLocation and getRequiredLocation methods.
     */
    public function testSetAndGetRequiredLocation()
    {
        $action = new TestAction('');
        $this->assertNull($action->getRequiredLocation());

        $location = $this->createStub(Location::class);
        $action->setRequiredLocation($location);

        $this->assertSame($location, $action->getRequiredLocation());
    }
}
