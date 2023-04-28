<?php

namespace App\Game;

// use App\Game\ReceiverInterface;
use App\Card\Card;

/**
 * Interface for implementing the most basic banker actions.
 */
interface BankerInterface
{
    /** @return bool As true if the banker should hit for another card, otherwise false. */
    public function keepHitting(): bool;

    /**
     * @param Card[] $removedCards All cards drawn in previous turns.
     * @param int    $playerPoints How much the player scored this turn.
     */
    public function passInfo(array $removedCards, int $playerPoints): void;
}
