<?php

/**
 * Hoverable pop-up
 */

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ticked_range')]
class TickedRangeComponent
{
    /**
     * @var string $label
     * @var string $name
     * @var int    $value
     * @var int    $min
     * @var int    $max
     * @var int    $step
     */
    public string $label = "";
    public string $name = "";
    public int $value;
    public int $min;
    public int $max;
    public int $step;

    /** @var int[] $values */
    public array $values = [];
}
