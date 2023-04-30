<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for HardBanker.
 */
class HardBankerTest extends TestCase
{
    /**
     * Provide data sets to test keepHitting method.
     * @return mixed[] As data set
     */
    public static function cardProvider(): array
    {
        return [
            'add 5 and 7 (less than player)'    => [[5, 7], 15, true],
            'add 10 and 8 (same as player)' => [[10, 8], 18, false],
            'add 7, 3 and 6 (more than player)' => [[7, 3, 6], 10, false],
        ];
    }

    /**
     * Test banker will keep hitting or stop after receiving cards and info about player.
     * @dataProvider cardProvider
     */
    public function testKeepHitting(array $ranks, int $playerPoints, bool $expected): void
    {
        $banker = new HardBanker();

        // Create and configure stubs to give banker
        foreach ($ranks as $rank) {
            $card = $this->createStub(\App\Card\Card::class);
            $card->method('getRank')
                ->willReturn($rank);
            $banker->receive($card);
        }

        $banker->passInfo([], $playerPoints);
        $res = $banker->keepHitting();
        $this->assertSame($expected, $res);
    }  
}
