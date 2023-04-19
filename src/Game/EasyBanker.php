<?php

namespace App\Game;

use App\Game\ReceiveTrait;
use App\Game\PassInfoTrait;
use App\Game\BankerInterface;

class EasyBanker implements BankerInterface
{
    use ReceiveTrait;

    use PassInfoTrait;

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
