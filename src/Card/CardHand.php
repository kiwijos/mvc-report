<?php

namespace App\Card;

use App\Card\Card;

/**
 * Hand of playing cards class.
 */
class CardHand
{
    /**
     * @var Card[] $hand Cards in this hand.
     */
    private array $hand;

    /**
     * Add card to hand.
     *
     * @param Card $card Card to add.
     */
    public function add(Card $card): void
    {
        $this->hand[] = $card;
    }

    /**
     * Set hand to array of cards.
     *
     * @param Card[] $cards Cards to set.
     */
    public function fromArray(array $cards): void
    {
        $this->hand = $cards;
    }

    /**
     * Get number of cards.
     *
     * @return int Number of cards.
     */
    public function getCount(): int
    {
        return count($this->hand);
    }

    /**
     * Get array of cards as strings.
     *
     * @return string[] Cards as strings.
     */
    public function getString(): array
    {
        return array_map('strval', $this->hand);
    }
}
