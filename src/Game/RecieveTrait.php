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
        $value = $card->getRank();

        // Check if dealt card is Ace and should score 1 or 14
        if ($value === 1 and $this->points <= 7) {
            $value = 14;
        }

        $this->points += $value;

        return $this->points;
    }
}