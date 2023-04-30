<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for GameManager and Player interaction.
 */
class GameManagerPlayerTest extends TestCase
{
    private GameManager $gameManager;

    protected function setUp(): void
    {
        // Create a test stub for Banker as some of the methods
        // used in these test cases will be dependent on having 
        // a banker defined
        $banker = $this->createStub(BankerInterface::class);

        $this->gameManager = new GameManager();
        $this->gameManager->setBanker($banker);
    }

    /**
     * Provide data sets to test dealPlayer method.
     * @return mixed[] As data set
     */
    public static function cardProvider(): array
    {
        return [
            'Deal 1 card (sum 13)'  => [1, 13],
            'Deal 2 cards (sum 25)' => [2, 25],
            'Deal 3 cards (sum 36)' => [3, 36],
        ];
    }

    /**
     * Test deal player cards.
     * Because the deck is ordered by default, we can expect which cards are drawn.
     * @dataProvider cardProvider
     * @param int $numCards
     * @param int $score
     */
    public function testDealPlayer(int $numCards, int $score): void
    {
        // Create actual deck
        $this->gameManager
            ->setDeck(new \App\Card\DeckOfCards);
        
        // Create actual player
        $player = new Player();
        $this->gameManager->setPlayer($player);

        // Deal given number of cards
        for ($i = 0; $i < $numCards; $i++) {
            $this->gameManager->dealPlayer();
        }
        
        $res = $this->gameManager->getState();

        /** @var \App\Card\Card[] $playerCards */
        $playerCards = $res["playerCards"];
        $this->assertCount($numCards, $playerCards);
        $this->assertSame($score, $res["playerPoints"]);
        $this->assertSame(52 - $numCards, $res["cardCount"]);
    }

    /**
     * Provide data sets to test getBurstRisk method.
     * @return mixed[] As data set
     */
    public static function valueProvider(): array
    {
        return [
            'No card will put the player over 21 — 0% risk'   => [7, 0, [], 0],
            '1 of 4 cards will put player over 21 - 25% risk' => [10, 4, [9, 10, 11, 12], 0.25],
            '1 of 2 cards will put player over 21 - 50% risk' => [10, 2, [9, 12], 0.5],
            'All cards will put player over 21 - 100% risk'   => [20, 3, [2, 3, 4], 1.0],
        ];
    }

    /**
     * Test calculate player burst risk.
     * @dataProvider valueProvider
     * @param int   $playerPoints
     * @param int   $deckCount
     * @param int[] $deckValues
     * @param float $expected
     */
    function testGetPlayerBurstRisk(int $playerPoints, int $deckCount, array $deckValues, float $expected): void
    {
        // Create and configure player stub
        $player = $this->createStub(ReceiverInterface::class);
        $player->method('getPoints')
            ->willReturn($playerPoints);

        // Create and configure deck stub
        $deck = $this->createStub(\App\Card\DeckOfCards::class);
        $deck->method('getCount')
            ->willReturn($deckCount);
        $deck->method('getValues')
            ->willReturn($deckValues);

        // Set up game manager
        $this->gameManager->setDeck($deck);
        $this->gameManager->setPlayer($player);

        $res = $this->gameManager->getBurstRisk();
        $this->assertSame($expected, $res);
    }

    /**
     * Test turning assistance mode on (true).
     */
    function testSetAssistanceMode(): void
    {
        $before = $this->gameManager->hasAssistanceMode();
        $this->assertFalse($before);

        $this->gameManager->setAssistanceMode(true);

        $after = $this->gameManager->hasAssistanceMode();
        $this->assertTrue($after);
    }
}
