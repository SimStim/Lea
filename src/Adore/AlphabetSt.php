<?php

declare(strict_types=1);

namespace Lea\Adore;

use DOMElement;
use Lea\Domain\Ebook;
use Lea\Domain\XMLetsGoCrazy;

/**
 * We're going down, down, down
 * If that's the only way
 * To make this cruel, cruel world
 * Hear what we've got to say
 * Put the right letters together
 * And make a better day
 */
class AlphabetSt
{
    public array $lut = [
        "toc" => "tableOfContents",
        "table of contents" => "tableOfContents",
        "list content" => "tableOfContents",
        "colophon" => "listRights",
        "text rights" => "listRights",
        "list text rights" => "listRights",
        "blurbs" => "listBlurbs",
        "list blurbs" => "listBlurbs",
        "text blurbs" => "listBlurbs",
    ];

    /**
     * Generates a table of contents and inserts it into the DOM structure.
     *
     * @param DOMElement $node The DOM node where the table of contents will be inserted. This node will be replaced.
     * @param Ebook $ebook The ebook object containing the texts from which the table of contents will be generated.
     * @return void
     */
    public function tableOfContents(DOMElement $node, Ebook $ebook): void
    {
        $toc = "<ol>" . PHP_EOL;
        foreach ($ebook->texts as $text)
            $toc .= "<li><lea:link>" . $text->title . "</lea:link></li>" . PHP_EOL;
        $toc .= "</ol>" . PHP_EOL;
        XMLetsGoCrazy::replaceNodeWithStringContent(node: $node, string: $toc);
    }

    /**
     * Iterates over the rights on a per-text-file basis and inserts them into the DOM structure.
     *
     * @param DOMElement $node The DOM node where the colophon will be inserted. This node will be replaced.
     * @param Ebook $ebook The ebook object containing the texts with rights information for generating the colophon.
     * @return void
     */
    public function listRights(DOMElement $node, Ebook $ebook): void
    {
        $rights = "";
        foreach ($ebook->texts as $text)
            if (!empty($text->rights))
                $rights .= $text->rights . PHP_EOL;
        XMLetsGoCrazy::replaceNodeWithStringContent(node: $node, string: $rights);
    }

    public function listBlurbs(DOMElement $node, Ebook $ebook): void
    {
        $class = $node->hasAttribute('heading-class')
            ? $node->getAttribute('heading-class')
            : "";
        $blurbs = "";
        foreach ($ebook->texts as $text)
            if (!empty($text->blurb))
                $blurbs .= "<h4 class='$class'><lea:link>" . $text->title . " by " . $text->authors[0]->name
                    . "</lea:link></h4>" . $text->blurb . PHP_EOL;
        XMLetsGoCrazy::replaceNodeWithStringContent(node: $node, string: $blurbs);
    }
}