<?php

use App\Adventure\Utils\AvailableActionsProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for AvailableActionsProvider class.
 */
class AvailableActionsProviderTest extends TestCase
{
    /**
     * Test case for getAvailableActions method.
     * Instead of asserting the exact array, as it might get updated,
     * validate the structure and properties of the returned array.
     */
    public function testGetAvailableActions(): void
    {
        $availableActions = AvailableActionsProvider::getAvailableActions();

        $this->assertIsArray($availableActions);

        foreach ($availableActions as $action) {
            $this->assertArrayHasKey('name', $action);
            $this->assertArrayHasKey('target', $action);
            $this->assertArrayHasKey('description', $action);

            $this->assertIsString($action['name']);
            $this->assertIsString($action['target']);
            $this->assertIsString($action['description']);
        }
    }

    /**
     * Test case for isValidAction method.
     */
    public function testIsValidAction(): void
    {
        // Test with valid actions
        $validActions = ['help', 'where', 'inventory', 'use', 'take', 'go', 'examine'];

        foreach ($validActions as $action) {
            $isValid = AvailableActionsProvider::isValidAction($action);
            $this->assertTrue($isValid);
        }

        // Test with invalid action
        $invalidAction = 'unknown';

        $isValid = AvailableActionsProvider::isValidAction($invalidAction);
        $this->assertFalse($isValid);
    }
}
