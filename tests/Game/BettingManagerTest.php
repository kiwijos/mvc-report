<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for setting up BettingManager class
 */
class BettingManagerTest extends TestCase
{
    /**
     * Test creating a new BettingManager without arguments.
     */
    function testCreateBettingManagerNoArguments(): void
    {
        $bettingManager = new BettingManager();
        $res = $bettingManager->getState();
        $this->assertSame(100, $res['playerCoins']);
        $this->assertSame(100, $res['bankerCoins']);
        $this->assertSame(5, $res['step']);
    }

    /**
     * Test creating a new BettingManager with arguments successfully.
     */
    function testCreateBettingManagerWithArgumentsOk(): void
    {
        $bettingManager = new BettingManager(50, 10);
        $res = $bettingManager->getState();
        $this->assertSame(50, $res['playerCoins']);
        $this->assertSame(50, $res['bankerCoins']);
        $this->assertSame(10, $res['step']);
    }

    /**
     * Provide data sets to test exception is raised when passing invalid value.
     */
    public static function invalidValueProvider(): array
    {
        return [
            'step larger than max'      => [10, 20],
            'step smaller than 1'       => [10, 0],
            'max not divisible by step' => [10, 7],
        ];
    }

    /**
     * Test creating a new BettingManager with invalid values passed to the constructor.
     * @dataProvider invalidValueProvider
     */
    function testCreateBettingManagerWithArgumentsRaisesException($max, $step): void
    {
        $this->expectException(\ValueError::class);
        $bettingManager = new BettingManager($max, $step);
    }

    /**
     * Test betting is off (false) by default.
     */
    function testIsBettingFalse(): void
    {
        $bettingManager = new BettingManager();
        $this->assertFalse($bettingManager->isBetting());
    }

    /**
     * Test turning betting on (true).
     */
    function testSetBettingTrue(): void
    {
        $bettingManager = new BettingManager();
        $bettingManager->setBetting(true);
        $this->assertTrue($bettingManager->isBetting());
    }
}
