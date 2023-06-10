<?php

namespace App\Adventure\InputActions;

/**
 * InputActionInterface represents an input action in the adventure game.
 * Classes implementing this interface should at least define the name identifying the action,
 * and the text response for when the action is performed.
 */
use App\Adventure\ActionResponses\LocationResponseInterface;

interface InputActionInterface
{
    /**
     * Retrieves the name that identifies the input action.
     * This is the name that the player has to input in order to perform the desired action in the game.
     *
     * @return string The name representing the input action.
     */
    public function getName(): string;

    /**
     * Retrieves the text response for the input action.
     *
     * @return string The text response for the input action.
     */
    public function getTextResponse(): string;

    /**
     * Retrieves the location response associated with the input action.
     *
     * @return LocationResponseInterface|null The location response object, or null if not set.
     */
    public function getLocationResponse(): ?LocationResponseInterface; 
}
