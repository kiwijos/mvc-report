<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Anax\TextFilter\TextFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('parseMarkdownFile', [$this, 'parseMarkdown']),
        ];
    }

    public function parseMarkdown(string $filename)
    {
        $text = file_get_contents($filename);
        $filter = new TextFilter();
        $parsed = $filter->parse($text, ["shortcode", "markdown"]);

        echo $parsed->text;
    }
}