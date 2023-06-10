<?php

namespace App\Adventure\InputActions;

/**
 * TakeAction implements InputActionInterface.
 * It represents the action that allows the player to take items.
 */
class TakeAction implements InputActionInterface
{
    use InputActionTrait;

    /**
     * Retrieves the name for the take action.
     *
     * @return string The name for the take action.
     */
    public function getName(): string
    {
        return 'take';
    }
}
