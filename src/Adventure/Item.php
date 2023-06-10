<?php

namespace App\Adventure;

use App\Adventure\InputActions\InputActionInterface;

/**
 * Represents an item in the adventure game.
 */
class Item
{
    /**
     * This is the name that the player has to input in order to perform actions on the item.
     * 
     * @var string The name of the item.
     */
    private $name;

    /**
     * @var string The description of the item.
     */
    private $description;

    /**
     * @var InputActionInterface[] An array of actions associated with the item.
     */
    private $actions;

    /**
     * @var bool Whether the item can be seen and interacted with when in a location.
     */
    private $hidden;

    /**
     * Item constructor.
     *
     * @param string $name        The name of the item.
     * @param string $description The description of the item.
     * @param bool   $hidden      Whether the item is hidden or not. Default is false.
     */
    public function __construct(string $name, string $description, bool $hidden = false)
    {
        $this->name = $name;
        $this->description = $description;
        $this->hidden = $hidden;
        $this->actions = [];
    }

    /**
     * Retrieves the name of the item.
     *
     * @return string The name of the item.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retrieves the description of the item.
     *
     * @return string The description of the item.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Checks if the item is hidden.
     *
     * @return bool True if the item is hidden, false otherwise.
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Sets the hidden status of the item.
     *
     * @param bool $value The hidden status to set.
     */
    public function setHidden(bool $value): void
    {
        $this->hidden = $value;
    }

    /**
     * Adds an action to the item.
     *
     * @param InputActionInterface $action The action to add.
     */
    public function addAction(InputActionInterface $action)
    {
        $this->actions[] = $action;
    }

    /**
     * Checks if the item has a specific action.
     *
     * @param string $actionName The name of the action.
     * @return bool True if the item has the action, false otherwise.
     */
    public function hasAction(string $actionName): bool
    {
        foreach ($this->actions as $action) {
            if ($action->getName() === $actionName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieves a specific action of the item.
     *
     * @param string $actionName The name of the action.
     * @return InputActionInterface|null The action object if found, or null if not found.
     */
    public function getAction(string $actionName): InputActionInterface
    {
        foreach ($this->actions as $action) {
            if ($action->getName() === $actionName) {
                return $action;
            }
        }

        return null;
    }
}
