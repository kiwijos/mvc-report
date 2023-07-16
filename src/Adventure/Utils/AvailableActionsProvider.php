<?php

namespace App\Adventure\Utils;

/**
 * Provides information about available actions in the game, including their names, targets, and descriptions.
 */
class AvailableActionsProvider
{
    /**
     * Retrieves the available actions with their corresponding descriptions.
     *
     * @return array<int, array<string, string>> An array of available actions, each containing 'name', 'target', and 'description'.
     */
    public static function getAvailableActions(): array
    {
        $availableActions = [
            [
                'name' => 'help',
                'target' => '<action>',
                'description' => 'Get help on available actions',
            ],
            [
                'name' => 'where',
                'target' => '',
                'description' => 'Recall where you are and view all exits and interactions in your current location',
            ],
            [
                'name' => 'inventory',
                'target' => '',
                'description' => 'View the items currently in your inventory'
            ],
            [
                'name' => 'examine',
                'target' => '<item>',
                'description' => 'Examine an item in your inventory or in the current location for more information',
            ],
            [
                'name' => 'take',
                'target' => '<item>',
                'description' => 'Take an item from the current location and add it to your inventory'
            ],
            [
                'name' => 'use',
                'target' => '<item>',
                'description' => 'Use an item from your inventory'
            ],
            [
                'name' => 'go',
                'target' => '<direction>',
                'description' => 'Move to a different location in the specified direction (e.g., north, south, east, west)',
            ],
        ];

        return $availableActions;
    }

    /**
     * Checks if a given action is valid.
     *
     * @param  string $action The action to validate.
     * @return bool   True if the action is valid, false otherwise.
     */
    public static function isValidAction(string $action): bool
    {
        $validActions = ['help', 'where', 'inventory', 'use', 'take', 'go', 'examine'];

        return in_array($action, $validActions);
    }
}
