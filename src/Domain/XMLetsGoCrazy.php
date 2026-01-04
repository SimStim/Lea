<?php

declare(strict_types=1);

namespace Lea\Domain;

use DOMDocument;
use DOMXPath;
use Lea\Girlfriend;
use NoDiscard;

/**
 * XMLetsGoCrazy class for static domain helpers
 *
 * We're all excited, but we don't know why.
 */
final class XMLetsGoCrazy
{
    private static string $leaNamespace = "https://logophilia.eu/lea";

    /**
     * Takes a file of XML fragments, including lea namespace directives,
     * and returns a DOMXPath object to inquire against
     *
     * @param string $fragments
     * @param string $fileName
     * @return DOMXPath
     */
    #[NoDiscard]
    public static function buildXPath(string $fragments, string $fileName): DOMXPath
    {
        $wrapped = "<xmletsgocrazy xmlns:lea='" . self::$leaNamespace . "'>$fragments</xmletsgocrazy>";
        $dom = new DOMDocument('1.0', 'UTF-8');
        if (!$dom->loadXML($wrapped, LIBXML_NONET)) {
            Girlfriend::comeToMe()->collectFallout("Malformed source xhtml document in file $fileName.");
            exit;
        }
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('lea', self::$leaNamespace);
        return $xpath;
    }

    /**
     * Extract the title from an XPath object
     *
     * @param DOMXPath $xpath
     * @param string $fileName
     * @return string
     */
    #[NoDiscard]
    public static function extractTitle(DOMXPath $xpath, string $fileName): string
    {
        if ($xpath->evaluate(expression: 'count(//lea:title) > 1')) {
            Girlfriend::comeToMe()->collectFallout("Multiple titles defined in $fileName; using first.");
        }
        return trim($xpath->evaluate(expression: 'string(//lea:title)'));
    }

    /**
     * Extract the author(s) from an XPath object
     *
     * @param DOMXPath $xpath
     * @param string $fileName
     * @return array
     */
    #[NoDiscard]
    public static function extractAuthors(DOMXPath $xpath, string $fileName): array
    {
        $nodes = $xpath->query(expression: "//lea:author");
        if ($nodes->length === 0) return [];
        $authors = [];
        foreach ($nodes as $node) {
            $name = trim($xpath->evaluate(expression: "string(lea:name)", contextNode: $node));
            if ($name === "") {
                Girlfriend::comeToMe()->collectFallout("Detected an invalid author tag in file $fileName.");
                continue;
            }
            $fileAs = trim($xpath->evaluate(expression: "string(lea:file-as)", contextNode: $node));
            $authors[] = new Author($name, $fileAs);
        }
        return $authors;
    }

    /**
     * Extract the optional blurb from an XPath object
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractBlurb(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: 'string(//lea:blurb)'));
    }

    /**
     * Extract the ISBN from an XPath object
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractISBN(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: 'string(//lea:isbn)'));
    }

    /**
     * Extract the texts from an XPath object
     *
     * @param DOMXPath $xpath
     * @return array
     */
    #[NoDiscard]
    public static function extractTexts(DOMXPath $xpath): array
    {
        $nodes = $xpath->query(expression: "//lea:text");
        if ($nodes->length === 0) return [];
        $texts = [];
        foreach ($nodes as $node)
            $texts[] = new Text(trim($node->textContent));
        return $texts;
    }
}
