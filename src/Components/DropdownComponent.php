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
     * @var bool   $hasHash    Indicates whether or not items should be appended to head by '#'
     * @var string $name       Name to display
     * @var string $routeAlias Path to route
     * @var array  $items      Items that are displayed when dropdown is expanded
     */
    public bool $hasHash = False;
    public string $name;
    public string $routeAlias;
    public array $items;
}
