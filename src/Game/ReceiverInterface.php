<?php

namespace App\Game;
use App\Card\Card;

/**
 * Interface for implementing the action of recieving a card and managing related properties.
 */
interface ReceiverInterface
{
    /** @param Card $card Dealt card to receive. */
    public function receive(Card $card): int;
    
    /** @return int As current points. */
    public function getPoints(): int;

    /** @return Card[] As current cards. */
    public function getCards(): array;

    /** Set points and cards back to their initial values. */
    public function reset(): void;
}
