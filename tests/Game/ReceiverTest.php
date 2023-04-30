<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for ReceiverTrait using Player class.
 */
class ReceiverTest extends TestCase
{
    /**
     * Provide data sets to test receive method.
     */
    public static function cardProvider(): array
    {
        return [
            'add 5, 2 and 3'           => [[5, 2, 3], 10],
            'add 8 and Ace (worth 1)'  => [[8, 1], 9],
            'add 7 and Ace (worth 14)' => [[7, 1], 21],
        ];
    }

    /**
     * Test receive cards.
     * @dataProvider cardProvider
     */
    #[DataProvider('cardProvider')]
    #[TestDox('$_dataName')]
    public function testReceiveCardsScoreOk(array $ranks, int $expected): void
    {
        $player = new Player();

        // Create and configure stubs to give player
        foreach ($ranks as $rank) {
            $card = $this->createStub(\App\Card\Card::class);
            $card->method('getRank')
                ->willReturn($rank);
            $player->receive($card);
        }
        
        // Assert added values match expected result
        $points = $player->getPoints();
        $this->assertSame($expected, $points);
    }

    /**
     * Test receive card method stores card.
     */
    function testGetCards(): void
    {
        $player = new Player();
        $card = $this->createStub(\App\Card\Card::class);

        $player->receive($card);
        $cards = $player->getCards();
        $this->assertSame([$card], $cards);
    }

    /**
     * Test reset method.
     */
    function testReset(): void
    {
        $player = new Player();

        // Create and configure stub
        $card = $this->createStub(\App\Card\Card::class);
        $card->method('getRank')
            ->willReturnOnConsecutiveCalls(5, 6, 7);

        $player->receive($card);
        $player->receive($card);
        $player->receive($card);

        $cardsBefore = $player->getCards();
        $pointsBefore = $player->getPoints();
        $this->assertCount(3, $cardsBefore);
        $this->assertNotEquals(0, $pointsBefore);

        $player->reset();                         // Reset properties to their initial values

        $cardsAfter = $player->getCards();
        $pointsAfter = $player->getPoints();
        $this->assertEmpty($cardsAfter);
        $this->assertSame(0, $pointsAfter);
    }
}