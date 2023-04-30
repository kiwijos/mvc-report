<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for when player or banker wins the stake.
 */
class WinsStakeTest extends TestCase
{
    /**
     * Provide data sets to test placeBet method.
     * @return mixed[] As data set
     */
    public static function winProvider(): array
    {
        return [
            'win — game not over' => [20, 50, false],
            'win — game over' => [100, 100, true],
        ];
    }

    /**
     * Test player wins stake.
     * @dataProvider winProvider
     * @param int  $bet
     * @param int  $max
     * @param bool $gameOver
     */
    function testPlayerWinsStake(int $bet, int $max, bool $gameOver): void
    {
        $bettingManager = new BettingManager($max);
        $bettingManager->placeBet($bet);
        $bettingManager->playerWinsStake();

        $res = $bettingManager->getState();
        $this->assertFalse($res['hasBet']);
        $this->assertSame(0, $res['stake']);
        $this->assertSame($max + $bet, $res['playerCoins']);
        $this->assertSame($gameOver, $res['gameOver']);
    }

    /**
     * Test banker wins stake.
     * @dataProvider winProvider
     * @param int  $bet
     * @param int  $max
     * @param bool $gameOver
     */
    function testBankerWinsStake(int $bet, int $max, bool $gameOver): void
    {
        $bettingManager = new BettingManager($max);
        $bettingManager->placeBet($bet);
        $bettingManager->bankerWinsStake();

        $res = $bettingManager->getState();
        $this->assertFalse($res['hasBet']);
        $this->assertSame(0, $res['stake']);
        $this->assertSame($max + $bet, $res['bankerCoins']);
        $this->assertSame($gameOver, $res['gameOver']);
    }
}
