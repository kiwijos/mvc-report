<?php

namespace App\Dice;

class DiceGraphic extends Dice
{
    /** @var string[] $representation */
    private array $representation = [
        '⚀',
        '⚁',
        '⚂',
        '⚃',
        '⚄',
        '⚅',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function getAsString(): string
    {
        return $this->representation[$this->value - 1];
    }
}
