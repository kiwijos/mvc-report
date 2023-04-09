<?php

namespace App\Card;

/**
 * Playing card class with a more graphical representation of suits and ranks.
 */
class CardGraphic extends Card
{
    /**
     * @var array $suits The different suits.
     * @var array $ranks The different ranks.
     */
    private $suits = array('♣', '♦', '♥', '♠');
    private $ranks = array('A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K');

    /**
     * Create a new Card object.
     *
     * @param int $suit Number to represent card suit.
     * @param int $rank Number to represent card rank.
     */
    public function __construct(int $suit, int $rank)
    {
        parent::__construct($suit, $rank);
    }

    /**
     * Return a string representation of this card.
     *
     * @return string Rank and suite of this card.
     */
    public function __toString(): string
    {
        return $this->ranks[$this->rank_value] . $this->suits[$this->suit_value];
    }
}
