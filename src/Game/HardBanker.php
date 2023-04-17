<?php

namespace App\Game;

use App\Game\RecieveTrait;
use App\Game\PassInfoTrait;
use App\Game\BankerInterface;

class HardBanker implements BankerInterface
{
    use RecieveTrait;

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
