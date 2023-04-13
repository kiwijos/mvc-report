<?php

namespace App\Card;

/**
 * Playing card class with a more graphical representation of suits
 * using unicode characters instead of literal names.
 */
class CardGraphic extends Card
{
    /** @var string[] $suits The different suits. */
    private array $suits = array('♣', '♦', '♥', '♠');

    /** @var string[] $ranks The different ranks. */
    private array $ranks = array('A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K');

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
     * @return string Rank and suite of this card as string.
     */
    public function __toString(): string
    {
        return $this->ranks[$this->rankValue] . $this->suits[$this->suitValue];
    }
}
