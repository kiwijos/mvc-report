<?php

namespace App\Game;

use App\Game\ReceiveTrait;
use App\Game\ReceiverInterface;

class Player implements ReceiverInterface
{
    use ReceiveTrait;
}
