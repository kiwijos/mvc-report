<?php

/**
 * Simple dropdown to test twig components
 */

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('dropdown')]
class DropdownComponent
{
    /**
     * @var bool   $hasHash Indicates whether or not items should be appended to head by '#'
     * @var string $head    Link that is always displayed at the top
     * @var array  $items   Links that are only displayed when dropdown is expanded
     */
    public bool $hasHash = False;
    public string $head;
    public array $items;
}
