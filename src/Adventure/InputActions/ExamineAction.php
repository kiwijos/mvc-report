<?php

namespace App\Adventure\InputActions;

/**
 * ExamineAction implements InputActionInterface.
 * It represents the action that allows the player to examine an item.
 */
class ExamineAction implements InputActionInterface
{
    use InputActionTrait;

    /**
    * Retrieves the name for the examine action.
    *
    * @return string The name for the examine action.
    */
    public function getName(): string
    {
        return 'examine';
    }
}
