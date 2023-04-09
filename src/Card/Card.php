<?php

namespace App\Card;

/**
 * Playing card class.
 */
class Card
{
    /**
     * @var int   $suit_value Number to represent card suit.
     * @var int   $rank_value Number to represent card rank.
     * @var array $suits      The different suits.
     * @var array $ranks      The different ranks.
     */
    protected $suit_value;
    protected $rank_value;
    private $suits = array('Clubs', 'Diamonds', 'Hearts', 'Spades');
    private $ranks = array('Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King');

    /**
     * Create a new Card object.
     *
     * @param int $suit Number to represent card suit.
     * @param int $rank Number to represent card rank.
     */
    public function __construct(int $suit, int $rank)
    {
        $this->suit_value = $suit;
        $this->rank_value = $rank;
    }

    /**
     * Get card suit.
     *
     * @return int Suit of this card. 
     */
    public function getSuit(): int
    {
        return $this->suit_value;
    }

    /**
     * Get card rank.
     *
     * @return int Rank of this card. 
     */
    public function getRank(): int
    {
        return $this->rank_value;
    }

    /**
     * Get a string representation of this card.
     *
     * @return string Rank and suite of this card. 
     */
    public function __toString(): string
    {
        return $this->ranks[$this->rank_value] . ' of ' . $this->suits[$this->suit_value];
    }
}
