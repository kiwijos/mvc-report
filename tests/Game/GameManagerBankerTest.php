<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for GameManager and Banker interaction.
 */
class GameManagerBankerTest extends TestCase
{
    private GameManager $gameManager;

    protected function setUp(): void
    {
        // Create a test stub for Player as some of the methods
        // used in these test cases will be dependent on having
        // a player defined
        $player = $this->createStub(ReceiverInterface::class);

        $this->gameManager = new GameManager();
        $this->gameManager->setPlayer($player);
    }

    /**
     * Test deal banker cards.
     * Because the deck is ordered by default, we can expect which cards are drawn.
     */
    public function testDealBanker(): void
    {
        // Create a small set of cards
        $cards = [];
        for ($i = 10; $i >= 1; $i--) {
            $cards[] = new \App\Card\Card($i % 4, $i);
        }

        // Create actual deck
        $this->gameManager->setDeck(new \App\Card\DeckOfCards($cards));

        // Use EasyBanker as it has the most straight forward interaction
        $this->gameManager->setBanker(new EasyBanker());

        // Because the banker will keep hitting unit it hits 17,
        // we can expect it to draw the cards 2, 3, 4, 5 and 6
        $this->gameManager->dealBanker();

        $res = $this->gameManager->getState();

        /** @var \App\Card\Card[] $bankerCards */
        $bankerCards = $res["bankerCards"];
        $this->assertCount(5, $bankerCards);
        $this->assertSame(20, $res["bankerPoints"]);
        $this->assertSame(5, $res["cardCount"]);
    }
}
