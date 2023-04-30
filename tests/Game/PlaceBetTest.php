<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for placing bets using BettingManager class.
 */
class PlaceBetTest extends TestCase
{
    /**
     * Provide data sets to test placeBet method.
     * @return mixed[] As data set
     */
    public static function betProvider(): array
    {
        return [
            'bet ok' => [20, 50, 30, true],
            'bet not ok — not enough coins' => [70, 50, 50, false],
            'bet not ok — incorrect step' => [12, 50, 50, false],
        ];
    }

    /**
     * Test placing bet.
     * @dataProvider betProvider
     * @param int  $bet
     * @param int  $max
     * @param int  $coinsLeft
     * @param bool $expected
     */
    public function testPlaceBet(int $bet, int $max, int $coinsLeft, bool $expected): void
    {
        $bettingManager = new BettingManager($max);
        $bettingManager->placeBet($bet);

        $res = $bettingManager->getState();
        $this->assertSame($expected, $res['hasBet']);

        $this->assertSame($coinsLeft, $res['playerCoins']);
        $this->assertSame($coinsLeft, $res['bankerCoins']);
    }

    /**
     * Provide data sets to test how placeBet method updates stake.
     * @return mixed[] As data set
     */
    public static function stakeProvider(): array
    {
        return [
            'bet ok - update stake '           => [20, 50, 40],
            'bet not ok — do not update stake' => [70, 50, 0],
        ];
    }

    /**
     * Test how stake is updated when placing bet.
     * @dataProvider stakeProvider
     * @param int $bet
     * @param int $max
     * @param int $expected
     */
    public function testPlaceBetGetStake(int $bet, int $max, int $expected): void
    {
        $bettingManager = new BettingManager($max);
        $bettingManager->placeBet($bet);
        $res = $bettingManager->getState();
        $this->assertSame($expected, $res['stake']);
    }

    /**
     * Test banker cannot match bet.
     */
    public function testBankerCannotMatchBet(): void
    {
        $bettingManager = new BettingManager(100);

        // Score is 100-100, place first bet and give win to player
        $bettingManager->placeBet(70);
        $bettingManager->playerWinsStake();

        // Score is now 170-30, place second bet
        $bettingManager->placeBet(50);

        $res = $bettingManager->getState();
        $expected = 80;
        $this->assertSame($expected, $res['stake']);
    }
}
