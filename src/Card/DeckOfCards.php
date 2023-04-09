<?php

namespace App\Card;

use App\Card\Card;

/**
 * Deck of playing cards class.
 */
class DeckOfCards
{
    /**
     * @var array $deck Array of cards.
     */
    private $deck;

    /**
     * Create new deck object.
     * 
     * @param array|null $deck Array of cards to create deck from if given.
     */
    public function __construct($deck = null)
    {
        if (is_array($deck)) {
            $this->deck = $deck;
            return;
        }

        foreach (range(0, 3) as $suite) {
            foreach (range(0, 12) as $rank) {
                $this->deck[] = new CardGraphic($suite, $rank);
            }
        }
    }

    /**
     * Add card to deck.
     * 
     * @param Card $card Card to add.
     */
    public function addCard(Card $card): void
    {
        $this->deck[] = $card;
    }

    /**
     * Get array of cards as strings.
     * 
     * @return array Cards as strings.
     */
    public function getString(): array
    {
        return array_map('strval', $this->deck);
    }

    /**
     * Get array of card objects.
     * 
     * @return array Card objects.
     */
    public function getDeck(): array
    {
        return $this->deck;
    }

    /**
     * Get number of cards.
     * 
     * @return int Number of cards.
     */
    public function getCount(): int
    {
        return count($this->deck);
    }

    /**
     * Shuffle cards.
     */
    public function shuffleCards(): void
    {
        shuffle($this->deck);
    }

    /**
     * Order cards by suit first then rank in ascending order.
     */
    public function sortBySuit(): void
    {
        usort($this->deck, function (Card $a, Card $b): int {
            return $a->getSuit() <=> $b->getSuit();
        });
    }

    /**
     * Order cards by rank first then suit in ascending order.
     */
    public function sortByRank(): void
    {
        usort($this->deck, function (Card $a, Card $b): int {
            return $a->getRank() <=> $b->getRank();
        });
    }

    /**
     * Get last number of cards and remove them from the deck.
     * 
     * @param  int   $num Number of cards to draw.
     * @return array      Drawn cards.
     */
    public function draw(int $num): array
    {
        return array_splice($this->deck, $num * -1);
    }
}
