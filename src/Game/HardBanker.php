<?php

namespace App\Game;

use App\Game\ReceiveTrait;
use App\Game\PassInfoTrait;
use App\Game\BankerInterface;
use App\Game\ReceiverInterface;

class HardBanker implements BankerInterface, ReceiverInterface
{
    use ReceiveTrait;

    use PassInfoTrait;

    /**
     * Returns whether or not the banker should keep hitting another card.
     * This banker knows how much the player scored.
     * 
     * @return bool As true if the banker has fewer points than the player, otherwise false
     */
    public function keepHitting(): bool
    {
        return $this->points < $this->playerPoints;
    }
}
