<?php

/**
 * Hoverable pop-up
 */

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('popup')]
class PopUpComponent
{
    public string $message;
    public string $arrow = 'left';
    public string $type;
}
