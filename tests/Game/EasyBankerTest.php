<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for EasyBanker class.
 */
class EasyBankerTest extends TestCase
{
    /**
     * Provide data sets to test keepHitting method.
     */
    public static function cardProvider(): array
    {
        return [
            'add 5 and 7 (under 17)'    => [[5, 7], true],
            'add 10 and 7 (exactly 17)' => [[10, 7], false],
            'add 10, 5 and 7 (over 17)' => [[10, 5, 7], false],
        ];
    }

    /**
     * Test banker will keep hitting or stop after receiving cards.
     * @dataProvider cardProvider
     */
    public function testKeepHitting(array $ranks, bool $expected): void
    {
        $banker = new EasyBanker();

        // Create and configure stubs to give banker
        foreach ($ranks as $rank) {
            $card = $this->createStub(\App\Card\Card::class);
            $card->method('getRank')
                ->willReturn($rank);
            $banker->receive($card);
        }

        $res = $banker->keepHitting();
        $this->assertSame($expected, $res);
    }  
}
