<?php

declare(strict_types=1);

namespace Lea\Domain;

use NoDiscard;
use DOMDocument;
use DOMXPath;

/**
 * XMLetsGoCrazy class for static domain helpers
 *
 * We're all excited, but we don't know why.
 */
final class XMLetsGoCrazy
{
    private(set) static string $leaNamespace = "https://logophilia.eu/lea";

    /**
     * Takes a file of XML fragments, including lea namespace directives,
     * and returns a DOMXPath object to inquire against
     *
     * @param string $fragments
     * @return DOMXPath
     */
    #[NoDiscard]
    public static function buildXPath(string $fragments): DOMXPath
    {
        $wrapped = "<xmletsgocrazy xmlns:lea='" . self::$leaNamespace . "'>$fragments</xmletsgocrazy>";
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($wrapped, LIBXML_NONET);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('lea', self::$leaNamespace);
        return $xpath;
    }

    /**
     * Checks an DOMXPath object and returns FALSE when the DOMDocument is empty
     * This would indicate:
     * - DOMDocument was not well-formed, or
     * - there has been a read error on the file (or file not found), or
     * - the XML fragments file did exist, but was actually devoid of any useful content
     *
     * @param DOMXPath $xpath
     * @return bool
     */
    #[NoDiscard]
    public static function isWellFormed(DOMXPath $xpath): bool
    {
        return $xpath->document->documentElement !== null;
    }

    /**
     * Extract the title from an XPath object
     * - <lea:title>The Gold Experience</lea:title>
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractTitle(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: 'string(//lea:title)'));
    }

    /**
     * Validates all existing <author> tags in passed XPath object
     *
     * @param DOMXPath $xpath
     * @return bool
     */
    public static function validateAuthors(DOMXPath $xpath): bool
    {
        $nodes = $xpath->query(expression: "//lea:author");
        if ($nodes->length === 0) return false; // at least one author required
        foreach ($nodes as $node) {
            $name = trim($xpath->evaluate(expression: "string(lea:name)", contextNode: $node));
            if ($name === "") return false; // invalid author detected (missing name)
        }
        return true;
    }

    /**
     * Extract the author(s) from an XPath object
     * - <lea:author>
     *     <lea:name>The Unpronounceable Symbol</lea:name>
     *     <lea:file-as>Nelson, Prince Rogers [The Unpronounceable Symbol]</lea:file-as>
     *   </lea:author>
     *
     * @param DOMXPath $xpath
     * @return array
     */
    #[NoDiscard]
    public static function extractAuthors(DOMXPath $xpath): array
    {
        $nodes = $xpath->query(expression: "//lea:author");
        $authors = [];
        foreach ($nodes as $node) {
            $name = trim($xpath->evaluate(expression: "string(lea:name)", contextNode: $node));
            if ($name === "") continue;
            $fileAs = trim($xpath->evaluate(expression: "string(lea:file-as)", contextNode: $node));
            $authors[] = new Author($name, $fileAs);
        }
        return $authors;
    }

    /**
     * Extract the optional blurb from an XPath object
     * - <lea:blurb>
     *     According to the NPG operator, the most beautiful girl in the world said, "I hate u." Thus commenced operation P. Control.
     *   </lea:blurb>
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
     * - ISBN-13 only. Not aware of any other formats going around for ebooks.
     * - <lea:isbn>987-1234567890</lea:isbn>
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
     * - file names are always relative to REPO
     * - sub folders are permitted
     * - <lea:text>tpsf-8/AboutTheAuthors.xhtml</lea:text>
     *
     * @param DOMXPath $xpath
     * @return array
     */
    #[NoDiscard]
    public static function extractTexts(DOMXPath $xpath): array
    {
        $nodes = $xpath->query(expression: "//lea:text");
        $texts = [];
        foreach ($nodes as $node)
            $texts[] = new Text(trim($node->textContent));
        return $texts;
    }
}
