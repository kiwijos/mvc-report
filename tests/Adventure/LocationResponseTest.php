<?php

use PHPUnit\Framework\TestCase;
use App\Adventure\ActionResponses\SwapLocationResponse;
use App\Adventure\ActionResponses\MoveLocationResponse;
use App\Adventure\Location;

/**
 * Unit tests for LocationResponses.
 */
class LocationResponseTest extends TestCase
{
    /**
     * Test case for SwapLocationResponse.
     */
    public function testSwapLocationResponse(): void
    {
        // Create a mock for the current location
        $currentLocation = $this->createStub(Location::class);

        // Create a mock for the new location
        $newLocation = $this->createStub(Location::class);

        // Create the swap location response instance
        $response = new SwapLocationResponse($newLocation);

        // Perform the location response action
        $resultLocation = $response->doLocationResponse($currentLocation);

        // Assert the new location is returned
        $this->assertSame($newLocation, $resultLocation);
    }

    /**
     * Test case for MoveLocationResponse.
     */
    public function testMoveLocationResponse(): void
    {
        // Create a mock for the new location
        $newLocation = $this->createStub(Location::class);

        // Create the move location response instance
        $response = new MoveLocationResponse($newLocation);

        // Perform the location response action (the current location is not needed for this test)
        $resultLocation = $response->doLocationResponse(null);

        // Assert the new location is returned
        $this->assertSame($newLocation, $resultLocation);
    }
}
