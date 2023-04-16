<?php

namespace App\Game;
use App\Card\Card;

/**
 * Interface for implementing the most basic banker actions.
 */
interface BankerInterface
{
    public function keepHitting(): bool;

    public function recieve(Card $card): int;
    
    public function getPoints(): int;

    public function getCards(): array;
}
