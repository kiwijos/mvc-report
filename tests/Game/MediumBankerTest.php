<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for MediumBanker.
 */
class MediumBankerTest extends TestCase
{
    /**
     * Provide data sets to test keepHitting method.
     */
    public static function cardProvider(): array
    {
        return [
            'add 3 and 4 (no risk of bursting)'             => [[3, 4], true],
            'add 4, 4 and 3 (low (≈ 24%) risk of bursting)' => [[4, 4, 3], true],
            'add 5, 5, and 4 (≈ 49% risk of bursting)'      => [[5, 5, 4], true],
            'add 2, 3, 4 and 5 (50% risk of bursting)'      => [[2, 3, 4, 5], true],
            'add 3, 3, 3, 4 and 1 (≈ 51% risk of bursting)' => [[3, 3, 3, 4, 1], false],
            'add 10 and 9 (high (84 %) risk of bursting)'   => [[10, 9], false],
        ];
    }

    /**
     * Test banker will keep hitting or stop after receiving cards.
     * @dataProvider cardProvider
     */
    public function testKeepHitting(array $ranks, bool $expected): void
    {
        $banker = new MediumBanker();

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

    /**
     * Provide data sets to test keepHitting method when some cards have previously been removed.
     */
    public static function cardsRemovedProvider(): array
    {
        return [
            'remove 10 and 5, add 6 and 8 (≈ 46% risk of bursting)'      => [[6, 8], [10, 5], true],
            'remove 7, 8 and 9, add 4, 5 and 6 (≈ 54% risk of bursting)' => [[4, 5, 6], [7, 8, 9], false]
        ];
    }

    /**
     * Test banker will keep hitting or stop after receiving cards.
     * @dataProvider cardsRemovedProvider
     */
    public function testKeepHittingCardsRemoved(array $ranks, array $removedCards, bool $expected): void
    {
        $banker = new MediumBanker();

        // Create and configure stubs to pass as removed cards
        $cardsToRemove = [];
        foreach ($removedCards as $rank) {
            $card = $this->createStub(\App\Card\Card::class);
            $card->method('getRank')
                ->willReturn($rank);
            $cardsToRemove[] = $card;
        }
        $banker->passInfo($cardsToRemove, 0);

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
