<?php

use PHPUnit\Framework\TestCase;
use App\Adventure\Utils\Connector;
use App\Adventure\Location;

/**
 * Unit tests for Connector class.
 */
class ConnectorTest extends TestCase
{
    /**
     * Test case for connectLocations method.
     */
    public function testConnectLocations()
    {
        // Create two real instances of the Location class
        $firstLocation = new Location('', '', '');
        $secondLocation = new Location('', '', '');

        // Create two partial mock instances of the Location class
        // to mock only the connectTo method
        $firstLocationMock = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor() // Do not invoke the constructor
            ->onlyMethods(['connectTo'])
            ->getMock();

        $secondLocationMock = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor() // Do not invoke the constructor
            ->onlyMethods(['connectTo'])
            ->getMock();

        // Manually invoke the connectTo method to make sure the logic is excecuted as expected,
        // whilest also making sure that the real instances are connected
        // (this allows us to test the outcome using the hasConnection method for example)
        $firstLocationMock->expects($this->once())
            ->method('connectTo')
            ->willReturnCallback(
                function ($location, $direction) use ($firstLocation) {
                    $firstLocation->connectTo($location, $direction);
                }
            );

        $secondLocationMock->expects($this->once())
            ->method('connectTo')
            ->willReturnCallback(
                function ($location, $direction) use ($secondLocation) {
                    $secondLocation->connectTo($location, $direction);
                }
            );

        // Connect the locations in both directions
        $resultLocation = Connector::connectLocations($firstLocationMock, $secondLocationMock, 'north');

        // Assertions
        $this->assertSame($firstLocationMock, $resultLocation);
        $this->assertTrue($firstLocation->hasConnection('north'));
        $this->assertSame($secondLocationMock, $firstLocation->getConnectedLocation('north'));
        $this->assertTrue($secondLocation->hasConnection('south'));
        $this->assertSame($firstLocationMock, $secondLocation->getConnectedLocation('south'));
    }

    /**
     * Test case for connectLocations method with already connected direction.
     */
    public function testConnectLocationsThrowsException()
    {
        // Create two mock instances of the Location class
        $firstLocation = $this->createMock(Location::class);
        $secondLocation = $this->createMock(Location::class);

        $firstLocation->expects($this->once())
            ->method('hasConnection')
            ->willReturn(true); // This effectively says that the locations are already connected

        // Try connecting the locations (direction does not matter)
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The direction 'north' is already connected to another location.");
        Connector::connectLocations($firstLocation, $secondLocation, 'north');
    }

    /**
     * Test case for swapLocations method.
     */
    public function testSwapLocations()
    {
        // Create mock instances of the Location class
        $oldLocation = $this->createMock(Location::class);
        $newLocation = $this->createMock(Location::class);

        // Create additional mock instances of the Location class for connections
        $connection1 = $this->createMock(Location::class);
        $connection2 = $this->createMock(Location::class);
        $connection3 = $this->createMock(Location::class);

        // Set up the connections for the old location
        $oldLocation->expects($this->once())
            ->method('getConnectedLocations')
            ->willReturn([
                'north' => $connection1,
                'west' => $connection2,
                'east' => $connection3
            ]);

        // Set up the disconnectFrom method for the connections
        $connection1->expects($this->once())
            ->method('disconnectFrom');
        $connection2->expects($this->once())
            ->method('disconnectFrom');
        $connection3->expects($this->once())
            ->method('disconnectFrom');

        // Set up the connectLocations method for the new location
        $newLocation->expects($this->exactly(3))
            ->method('connectTo');

        // Perform the swap locations action
        $resultLocation = Connector::swapLocations($oldLocation, $newLocation);

        // Assertions
        $this->assertSame($newLocation, $resultLocation);
    }
}
