<?php

namespace App\Card;

/**
 * Playing card class.
 */
class Card
{
    /**
     * @var int $suitValue Number to represent card suit.
     * @var int $rankValue Number to represent card rank.
     */
    protected int $suitValue;
    protected int $rankValue;

    /** @var string[] $suits The different suits. */
    private array $suits = array('Clubs', 'Diamonds', 'Hearts', 'Spades');

    /** @var string[] $ranks The different ranks. */
    private array $ranks = array('Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King');

    /**
     * Create a new Card object.
     *
     * @param int $suit Number to represent card suit.
     * @param int $rank Number to represent card rank.
     */
    public function __construct(int $suit, int $rank)
    {
        $this->suitValue = $suit;
        $this->rankValue = $rank;
    }

    /**
     * Get card suit.
     *
     * @return int Suit of this card.
     */
    public function getSuit(): int
    {
        return $this->suitValue;
    }

    /**
     * Get card rank.
     *
     * @return int Rank of this card.
     */
    public function getRank(): int
    {
        return $this->rankValue;
    }

    /**
     * Get a string representation of this card.
     *
     * @return string Rank and suite of this card as a string.
     */
    public function __toString(): string
    {
        return $this->ranks[$this->rankValue] . ' of ' . $this->suits[$this->suitValue];
    }
}
