<?php

namespace App\Game;

/**
 * Trait for implementing the action of passing information from game manager to banker.
 */
trait PassInfoTrait
{
    /** @var int $playerPoints */
    private int $playerPoints = 0;

    /** @var Card[] $removedCards */
    private array $removedCards = [];

    /** 
     * @param Card[] $removedCards Cards removed from the deck.
     * @param int    $playerPoints Players current points.
     */
    public function passInfo(array $removedCards, int $playerPoints): void
    {
        $this->removedCards = $removedCards;
        $this->playerPoints = $playerPoints;
    }
}