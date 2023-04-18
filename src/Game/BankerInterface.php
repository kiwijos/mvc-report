<?php

namespace App\Game;
use App\Game\ReceiverInterface;

/**
 * Interface for implementing the most basic banker actions.
 */
interface BankerInterface extends ReceiverInterface
{
    /** @return bool As true if the banker should hit for another card, otherwise false. */
    public function keepHitting(): bool;

    /** 
     * @param Card[] $removedCards All cards drawn in previous turns.
     * @param int    $playerPoints How much the player scored this turn.
     */
    public function passInfo(array $removedCards, int $playerPoints): void;
}
