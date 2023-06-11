<?php

namespace App\Adventure\InputActions;

use App\Adventure\Location;
use App\Adventure\ActionResponses\LocationResponseInterface;

/**
 * The InputActionTrait provides common functionality for input actions in the game.
 */
trait InputActionTrait
{
    /**
     * @var string The text response for the input action.
     */
    private $textResponse;
    
    /**
     * @var LocationResponseInterface|null The location response object for the input action.
     */
    private $locationResponse;

    /**
     * @var Location|null The location required for the action to work.
     */
    private $requiredLocation;

    /**
     * @var bool Whether the action can be repeated.
     */
    private $repeatable;

    /**
     * InputAction constructor.
     *
     * @param string $textResponse The text response for the input action.
     * @param bool   $textResponse Whether the action is repeatable. Default is false.
     */
    public function __construct(string $textResponse, bool $repeatable = false)
    {
        $this->textResponse = $textResponse;
        $this->repeatable = $repeatable;
        $this->locationResponse = null;
        $this->requiredLocation = null;
    }

    /**
     * Retrieves the text response for the input action.
     *
     * @return string The text response.
     */
    public function getTextResponse(): string
    {
        return $this->textResponse;
    }

    /**
     * Sets the location response object for the input action.
     *
     * @param LocationResponseInterface|null $locationResponseObject The location response object, or null.
     */
    public function setLocationResponse(?LocationResponseInterface $locationResponseObject): void
    {
        $this->locationResponse = $locationResponseObject;
    }

    /**
     * Retrieves the location response object for the input action.
     *
     * @return LocationResponseInterface|null The location response object, or null if not set.
     */
    public function getLocationResponse(): ?LocationResponseInterface
    {
        return $this->locationResponse;
    }

    /**
     * Sets the location required for the action to work.
     *
     * @param Location|null $location The required location, or null.
     */
    public function setRequiredLocation(?Location $location): void
    {
        $this->requiredLocation = $location;
    }

    /**
     * Retrieves the location required for the action to work.
     *
     * @return Location|null The required location, or null if not set.
     */
    public function getRequiredLocation(): ?Location
    {
        return $this->requiredLocation;
    }

    /**
     * Checks whether the player should be allowed to repeat the action
     * more than once.
     *
     * @return bool Whether the action is repeatable.
     */
    public function isRepeatable(): bool
    {
        return $this->repeatable;
    }
}
