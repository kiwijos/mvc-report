<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for GameManager getHasWon and updateHasWonStatus methods.
 */
class GameHasWonTest extends TestCase
{
    /**
     * Provide data sets to test who has won.
     * @return mixed[] As data set
     */
    public static function pointsProvider(): array
    {
        return [
            'Player over 21 — Banker wins'     => [22, 0, -1],
            'Player scores 21 — Player wins'   => [21, 0, 1],
            'Player over banker — Player wins' => [20, 18, 1],
            'Banker over 21 — Player wins'     => [15, 23, 1],
            'Banker over player — Banker wins' => [14, 17, -1],
            'Same score — Banker wins'         => [19, 19, -1],
            'Only player score — No winner'    => [16, 0, 0],
        ];
    }
    /**
     * Test different cases of who has won.
     * @dataProvider pointsProvider
     * @param int $playerPoints
     * @param int $bankerPoints
     * @param int $expected
     */
    function testHasWon(int $playerPoints, int $bankerPoints, int $expected):void
    {
        // Create and configure stubs
        $player = $this->createStub(ReceiverInterface::class);
        $player->method('getPoints')
            ->willReturn($playerPoints);
        $banker = $this->createStub(BankerInterface::class);
        $banker->method('getPoints')
            ->willReturn($bankerPoints);

        // Set up game manager
        $gameManager = new GameManager();
        $gameManager->setPlayer($player);
        $gameManager->setBanker($banker);

        // Act and assert
        $gameManager->updateHasWonStatus();
        $res = $gameManager->getHasWon();
        $this->assertSame($expected, $res);
    }
}
