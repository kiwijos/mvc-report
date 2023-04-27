<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Anax\TextFilter\TextFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('parseMarkdownFile', [$this, 'parseMarkdown']),
        ];
    }

    /**
     * Ouput HTML from markdown.
     *
     * @param string $filename Markdown file to parse.
     */
    public function parseMarkdown(string $filename): void
    {
        $text = file_get_contents($filename);
        $filter = new TextFilter();
        $parsed = $filter->parse($text, ["shortcode", "markdown"]); // @phpstan-ignore-line

        echo $parsed->text; // @phpstan-ignore-line
    }
}
