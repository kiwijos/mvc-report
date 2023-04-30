<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Card and CardGraphic. Note that testing CardGraphic will cover parts of the base class too.
 * To avoid duplicate code, CardGraphic is therefore used instead of the base class in most test cases.
 */
class CardTest extends TestCase
{
    /**
     * Test create new Card object.
     */
    public function testCreateCard(): void
    {
        $card = new CardGraphic(1, 1);
        $this->assertInstanceOf('App\Card\Card', $card);
    }

    /**
     * Test get actual rank integer value.
     * Note that the actual rank of a card is different from the value passed to the constructor.
     * That initial value can be 0 (as in this case) and is used for indexing inside the class.
     * No real playing card has a rank of 0 though, so the getRank method increments it by one.
     */
    public function testGetRank(): void
    {
        $card = new CardGraphic(42, 0);
        $res = $card->getRank();
        $exp = 1;
        $this->assertSame($exp, $res);
    }

    /**
     * Test get suit integer value.
     */
    public function testGetSuit(): void
    {
        $card = new CardGraphic(1, 2);
        $res = $card->getSuit();
        $exp = 1;
        $this->assertSame($exp, $res);
    }

    /**
     * Test get Card string representation.
     */
    public function testCardToString(): void
    {
        $card = new Card(1, 2);
        $res = $card->__toString();
        $exp = '3 of Diamonds';
        $this->assertSame($exp, $res);
    }

    /**
     * Test get CardGraphic string representation.
     */
    public function testCardGraphicToString(): void
    {
        $card = new CardGraphic(1, 2);
        $res = $card->__toString();
        $exp = '3♦';
        $this->assertSame($exp, $res);
    }
}
