<?php

/**
 * Hoverable pop-up
 */

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ticked_range')]
class TickedRangeComponent
{
    public string $label = "";
    public string $name = "";
    public int $value;
    public int $min;
    public int $max;
    public int $step;
    public array $values = [];
}
