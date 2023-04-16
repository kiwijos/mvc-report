<?php

namespace App\Game;

use App\Game\RecieveTrait;
use App\Game\BankerInterface;

class EasyBanker implements BankerInterface
{
    use RecieveTrait;

    /**
     * Returns whether or not the banker should keep hitting another card.
     * 
     * @return bool As true if banker has not hit 17 or higher, otherwise false.
     */
    public function keepHitting(): bool
    {
        return $this->points < 17;
    }
}
