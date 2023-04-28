<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class DeckOfCards.
 */
class DeckOfCardsTest extends TestCase
{
    /**
     * Test create new object no argument. Expect a deck of 52 Card objects.
     */
    function testCreateDeckNoArgument(): void
    {
        $deck = new DeckOfCards();
        $cards = $deck->getDeck();

        $this->assertCount(52, $cards);
        $this->assertContainsOnlyInstancesOf(Card::class, $cards);
    }

    /**
     * Test create new object with argument specifying an array of Card objects.
     */
    function testCreateDeckWithArgument(): void
    {
        // Create a test stub for Card
        $card = $this->createStub(Card::class);

        $deck = new DeckOfCards([$card, $card, $card]);

        $cards = $deck->getDeck();

        $this->assertSame([$card, $card, $card], $cards);
    }

    /**
     * Test create new object with argument specifying an empty array.
     */
    function testCreateEmptyDeck(): void
    {
        $deck = new DeckOfCards([]);
        $cards = $deck->getDeck();
        $this->assertEmpty($cards);
    }

    /**
     * Test add card.
     */
    function testAddCard(): void
    {
        $deck = new DeckOfCards([]); // Create empty deck
        
        // Create a test stub for Card
        $card = $this->createStub(Card::class);

        $deck->addCard($card);
        $deck->addCard($card);
        $deck->addCard($card);

        $cards = $deck->getDeck();

        $this->assertSame([$card, $card, $card], $cards);
    }

    /**
     * Test get cards as array of strings.
     */
    function testGetCardsAsString(): void
    {
        // Create a test stub for Card
        $card = $this->createStub(Card::class);

         // Configure the test stub
        $card->method('__toString')
            ->willReturnOnConsecutiveCalls('first', 'second', 'third');
        
        $deck = new DeckOfCards([$card, $card, $card]);

        $res = $deck->getString();

        $this->assertSame(['first', 'second', 'third'], $res);
    }

    /**
     * Test get array of card values.
     */
    function testGetCardValues(): void
    {
        // Create a test stub for Card
        $card = $this->createStub(Card::class);

         // Configure the test stub
        $card->method('getRank')
            ->willReturnOnConsecutiveCalls(1, 2, 3);
        
        $deck = new DeckOfCards([$card, $card, $card]);

        $res = $deck->getValues();
        $this->assertSame([1, 2, 3], $res);
    }

    /**
     * Test get card count. 
     */
    function testGetCardCount(): void
    {
         $card = $this->createStub(Card::class);         // Create a test stub for Card

         $deck = new DeckOfCards([$card, $card, $card]);

         $count = $deck->getCount();
         $this->assertSame(3, $count);
    }

    /**
     * Test drawing a single card.
     */
    function testDrawSingleCardIsSame(): void
    {
        $card = $this->createStub(Card::class); // Create a test stub for Card

        $deck = new DeckOfCards([$card]);       // Create deck with single card

        $draw = $deck->draw(1);                 // Draw single card
        $this->assertSame([$card], $draw);      // Assert it is the same

        $cards = $deck->getDeck();              // Get cards left in deck
        $this->assertEmpty($cards);             // Assert deck is now empty
    }

    function testDrawMultipleCardsCountOk()
    {
        $deck = new DeckOfCards();              // Create full deck of 52 cards

        $draw = $deck->draw(3);                 // Draw three cards
        $this->assertCount(3, $draw);           // Assert count is correct

        $cards = $deck->getDeck();              // Get cards left in deck
        $this->assertCount(49, $cards);         // Assert deck has three fewer cards   
    }

    /**
     * 
     */
    function testDrawMultipleCardsOrderIsOk()
    {
        $deck = new DeckOfCards();              // Create full deck of 52 cards

        $draw = $deck->draw(3);                 // Draw three cards
        
        $res = array_map('strval', $draw);      // Get string representation of each card

        // Because deck is ordered by default,
        // we can expect draw to be King, Queen and Jack of Spades
        $this->assertSame(['K♠', 'Q♠', 'J♠'], $res);
    }

    /**
     * Test shuffle cards.
     */
    function testShuffleCards(): void
    {
        srand(42);                                  // Seed the random number generator to get reproducible results

        $deck = new DeckOfCards([]);

        // Create a test stubs for Card
        for ($i = 0; $i < 10; $i++) {
            $card = $this->createStub(Card::class);
            $card->method('getRank')
                ->willReturn($i);
            $deck->addCard($card);
        }

        $deck->shuffleCards();
        $res = $deck->getValues();
        $exp = [1, 3, 9, 7, 6, 0, 8, 4, 5, 2];
        $this->assertSame($exp, $res);
    }
}
