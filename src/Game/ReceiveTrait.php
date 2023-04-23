<?php

namespace App\Game;

use App\Card\Card;

/**
 * Trait for implementing the basic action of recieving a dealt card and accessing properties.
 */
trait ReceiveTrait
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
     * Receive a dealt card and add points.
     * 
     * @param Card $card
     */
    public function receive(Card $card): void
    {
        $this->cards[] = $card;
        $value = $card->getRank();

        // Check if dealt card is Ace and should score 1 or 14
        if ($value === 1 and $this->points <= 7) {
            $value = 14;
        }

        $this->points += $value;
    }

    /**
     * Set propertiess to their initial values.
     */
    public function reset(): void
    {
        $this->points = 0;
        $this->cards = [];
    }
}
