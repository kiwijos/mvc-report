<?php

/**
 * Simple component to gather all pages
 */

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('navbar')]
class NavbarComponent
{
    /**
     * @var mixed[] $pages List of all pages and subpages in dropdown
     */
    public array $pages = array(
        [
            'name' => 'Home',
            'routeAlias' => 'index',
            'dropdown' => false
        ],
        [
            'name' => 'About',
            'routeAlias' => 'about',
            'dropdown' => false
        ],
        [
            'name' => 'Report',
            'routeAlias' => 'report',
            'dropdown' => [
                'kmom01' => 'Kmom01',
                'kmom02' => 'Kmom02',
                'kmom03' => 'Kmom03',
                'kmom04' => 'Kmom04',
                'kmom05' => 'Kmom05',
                'kmom06' => 'Kmom06',
                'kmom10' => 'Kmom10',
            ],
            'hasHash' => true,
        ],
        [
            'name' => 'Work',
            'routeAlias' => 'work',
            'dropdown' => [
                'lucky' => 'Lucky Number',
                'pig_index' => 'Pig Game',
                'card_index' => 'Card Game',
                'app_product' => 'Product'
            ],
            'hasHash' => false,
        ],
        [
            'name' => 'API',
            'routeAlias' => 'api',
            'dropdown' => [
                'quote' => 'Daily Quote',
                'json_card_index' => 'JSON Card',
                'json_game' => 'JSON Game',
                'json_library' => 'JSON Library'
            ],
            'hasHash' => false,
        ],
        [
            'name' => 'Game',
            'routeAlias' => 'game_index',
            'dropdown' => [
                'game_init' => 'Play',
                'game_docs' => 'Docs',
            ],
            'hasHash' => false,
        ],
        [
            'name' => 'Library',
            'routeAlias' => 'library_index',
            'dropdown' => [
                'library_read_many' => 'Show All Books',
            ],
            'hasHash' => false,
        ],
        [
            'name' => 'Metrics',
            'routeAlias' => 'metrics',
            'dropdown' => false,
        ],
        [
            'name' => 'Project',
            'routeAlias' => 'proj',
            'dropdown' => false,
        ],
    );
}
