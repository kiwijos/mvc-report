<?php

namespace App\Game;
use App\Card\Card;

/**
 * Trait for implementing the basic action of recieving a dealt card and accessing properties.
 */
trait RecieveTrait
{
    /** @var int $points */
    private int $points = 0;

    /** @var Card[] $cards */
    private array $cards = [];

    /** @return int As total points */
    public function getPoints(): int
    {
        return $this->points;
    }

    /** @return Card[] As total cards */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * @param Card $card
     * 
     * @return int As total points
     */
    public function recieve(Card $card): int
    {
        $this->cards[] = $card;
        $this->points += $card->getRank();

        return $this->points;
    }
}