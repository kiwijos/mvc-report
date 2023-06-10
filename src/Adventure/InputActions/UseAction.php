<?php

namespace App\Adventure\InputActions;

/**
 * UseAction implements InputActionInterface.
 * It represents the action that allows the player to use items.
 */
class UseAction implements InputActionInterface
{
    use InputActionTrait;

    /**
     * Retrieves the name for the use action.
     *
     * @return string The name for the use action.
     */
    public function getName(): string
    {
        return 'use';
    }
}
