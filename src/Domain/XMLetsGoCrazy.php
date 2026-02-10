<?php

declare(strict_types=1);

namespace Lea\Domain;

use NoDiscard;
use DOMDocument;
use DOMException;
use DOMImplementation;
use DOMXPath;
use Lea\Adore\Girlfriend;

/**
 * XMLetsGoCrazy class for static domain helpers
 *
 * We're all excited, but we don't know why.
 */
final class XMLetsGoCrazy
{
    private(set) static string $leaNamespace = "https://logophilia.eu/lea/2026/xhtml";
    private(set) static string $rootElement = "xmletsgocrazy";

    /**
     * Takes a DOMDocument wrapped in the lea namespace
     * and returns a DOMXPath object to inquire against
     *
     * @param DOMDocument $dom
     * @return DOMXPath
     */
    #[NoDiscard]
    public static function createXPath(DOMDocument $dom): DOMXPath
    {
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace(prefix: 'lea', namespace: self::$leaNamespace);
        return $xpath;
    }

    /**
     * Takes an xhtml string and returns a DOMDocument object
     *
     * @param string $xhtml
     * @return DOMDocument
     */
    #[NoDiscard]
    public static function createDOM(string $xhtml): DOMDocument
    {
        $dom = new DOMDocument(version: '1.0', encoding: 'UTF-8');
        $dom->loadXML($xhtml, options: LIBXML_NONET);
        return $dom;
    }

    /**
     * Takes a file of XML fragments, including lea namespace directives,
     * and returns a DOMDocument object
     *
     * @param string $fragments
     * @return DOMDocument
     */
    #[NoDiscard]
    public static function createDOMFromFragments(string $fragments): DOMDocument
    {
        $wrapped = "<" . self::$rootElement .
            " xmlns:lea='" . self::$leaNamespace . "'>$fragments</" . self::$rootElement . ">";
        return self::createDOM($wrapped);
    }

    /**
     * Checks a DOMXPath object and returns FALSE when the DOMDocument is empty
     * This would indicate:
     * - DOMDocument was not well-formed, or
     * - There has been a read error on the file (or file not found), or
     * - The XML fragments file did exist but was actually devoid of any useful content
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
        return trim($xpath->evaluate(expression: "string(/" . self::$rootElement . "/lea:title)"));
    }

    /**
     * Extract the description from an XPath object
     * - <lea:description>The Pitch Science Fiction is a quarterly publication of science-fiction stories
     *                    by various authors, covering diverse themes.</lea:description>
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractDescription(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: "string(/" . self::$rootElement . "/lea:description)"));
    }

    /**
     * Validates all existing <author> tags in a passed XPath object
     *
     * @param DOMXPath $xpath
     * @return bool
     */
    public static function validateAuthors(DOMXPath $xpath): bool
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:author");
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
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:author");
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
     * Extract the rights from an XPath object
     * - <lea:rights>English translation of M2 (C) 2025 by Eduard Pech.
     *               Published by permission of Daria Skrinitsa.
     *               All other stories are in the public domain in the USA.
     *               All content not marked as public domain (C) 2025 by Eduard Pech.
     *               Complete edition (C) 2025 by Logophilia.
     *               Logophilia is an imprint of Logophilia OÜ.</lea:rights>
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractRights(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: "string(/" . self::$rootElement . "/lea:rights)"));
    }

    /**
     * Extract the language from an XPath object
     * - <lea:language>en</lea:language>
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractLanguage(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: "string(/" . self::$rootElement . "/lea:language)"));
    }

    /**
     * Extract the dates from an XPath object
     * - <lea:date>
     *     <lea:created>The Unpronounceable Symbol</lea:created>
     *     <lea:modified>Nelson, Prince Rogers [The Unpronounceable Symbol]</lea:modified>
     *     <lea:issued>Nelson, Prince Rogers [The Unpronounceable Symbol]</lea:issued>
     *   </lea:date>
     *
     * @param DOMXPath $xpath
     * @return Date
     */
    #[NoDiscard]
    public static function extractDate(DOMXPath $xpath): Date
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:date");
        if ($nodes === false || $nodes->length !== 1) return new Date ();
        $created = trim($xpath->evaluate(expression: "string(lea:created)", contextNode: $nodes[0]));
        $modified = trim($xpath->evaluate(expression: "string(lea:modified)", contextNode: $nodes[0]));
        $issued = trim($xpath->evaluate(expression: "string(lea:issued)", contextNode: $nodes[0]));
        return new Date (created: $created, modified: $modified, issued: $issued);
    }

    /**
     * Extract the publisher from an XPath object
     * - <lea:publisher>
     *     <lea:imprint>Logophilia</lea:imprint>
     *     <lea:contact>origins@logophilia.eu</lea:contact>
     *   </lea:publisher>
     * This information is not mandatory, according to specifications,
     * but I choose to print annoying messages until the tags are present.
     * All data is free-form text and not checked for formatting.
     * For example, contact could be a telephone number,
     * or just the string, "go fuck yourself, Lea."
     *
     * @param DOMXPath $xpath
     * @return Publisher
     */
    #[NoDiscard]
    public static function extractPublisher(DOMXPath $xpath): Publisher
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:publisher");
        if ($nodes === false || $nodes->length !== 1) return new Publisher();
        $imprint = trim($xpath->evaluate(expression: "string(lea:imprint)", contextNode: $nodes[0]));
        $contact = trim($xpath->evaluate(expression: "string(lea:contact)", contextNode: $nodes[0]));
        return new Publisher(imprint: $imprint, contact: $contact);
    }

    /**
     * Validates all existing <contributor> tags in a passed XPath object
     *
     * @param DOMXPath $xpath
     * @param array $reference
     * @return bool
     */
    public static function validateContributors(DOMXPath $xpath, array $reference): bool
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:contributor");
        $ptr = 0;
        foreach ($nodes as $node) {
            $name = trim($xpath->evaluate(expression: "string(lea:name)", contextNode: $node));
            if ($name === "") return false;
            $roleNodes = $xpath->query(expression: "lea:role", contextNode: $node);
            if (count($roleNodes) !== $reference[$ptr++]) return false; // divergence of found versus expected roles
            foreach ($roleNodes as $roleNode)
                if (!in_array(strtolower(trim($roleNode->textContent)), Contributor::$permittedRoles, true)) return false;
        }
        return true;
    }

    /**
     * Extract the contributor(s) from an XPath object
     * - <lea:contributor>
     *     <lea:name>The Unpronounceable Symbol</lea:name>
     *     <lea:role>edt</lea:role>
     *     <lea:role>trl</lea:role>
     *     <lea:role>bkp</lea:role>
     *   </lea:contributor>
     *
     * @param DOMXPath $xpath
     * @return array
     */
    #[NoDiscard]
    public static function extractContributors(DOMXPath $xpath): array
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:contributor");
        $contributors = [];
        foreach ($nodes as $node) {
            $name = trim($xpath->evaluate(expression: "string(lea:name)", contextNode: $node));
            if ($name === "") continue;
            $roleNodes = $xpath->query(expression: "lea:role", contextNode: $node);
            $roles = [];
            foreach ($roleNodes as $roleNode)
                $roles[] = strtolower(trim($roleNode->textContent));
            $roles = array_intersect($roles, Contributor::$permittedRoles); // use only permitted roles
            if (empty($roles)) continue; // at least one role is required
            $contributors[] = new Contributor($name, $roles);
        }
        return $contributors;
    }

    /**
     * Extract the optional blurb from an XPath object
     * - <lea:blurb>
     *     According to the NPG operator, the most beautiful girl in the world said, "I hate u."
     *     Thus commenced operation P. Control.
     *   </lea:blurb>
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractBlurb(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: "string(/" . self::$rootElement . "/lea:blurb)"));
    }

    /**
     * Extract the cover from an XPath object
     * - <lea:cover>2025Q4-cover-inside.jpg</lea:cover>
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractCover(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: "string(/" . self::$rootElement . "/lea:cover)"));
    }

    /**
     * Extract the image file names from an XPath object
     * - <lea:image>Harry-Harrison-FM-Logophilia-512.jpg</lea:image>
     * - <lea:image>Hubris-cover-512-qr.jpg</lea:image>
     *
     * Returns false for missing or incomplete <lea:file> declarations.
     *
     * @param DOMXPath $xpath
     * @return array|false
     */
    #[NoDiscard]
    public static function extractImages(DOMXPath $xpath): array|false
    {
        $nodes = $xpath->query(expression: "//lea:image");
        $images = [];
        foreach ($nodes as $node) {
            $fileName = trim($xpath->evaluate(expression: "string(lea:file)", contextNode: $node));
            if ($fileName === "") return false;
            $caption = trim($xpath->evaluate(expression: "string(lea:caption)", contextNode: $node));
            if ($caption === "default") $caption = Girlfriend::comeToMe()->recall(name: "defaultcaption");
            $images[] = new Image($fileName, $caption);
        }
        return $images;
    }

    /**
     * Extract the optional subjects from an XPath object
     * - <lea:subject>Diplomats -- Fiction</lea:subject>
     * - <lea:subject>Extraterrestrial beings -- Fiction</lea:subject>
     *
     * @param DOMXPath $xpath
     * @return array
     */
    #[NoDiscard]
    public static function extractSubjects(DOMXPath $xpath): array
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:subject");
        $subjects = [];
        foreach ($nodes as $node)
            $subjects[] = trim($node->textContent);
        return $subjects;
    }

    /**
     * Extract the stylesheet file names from an XPath object
     * - <lea:stylesheet>lphi-originals/stylesheet.css</lea:stylesheet>
     * - <lea:stylesheet>lphi-originals/fonts.css</lea:stylesheet>
     *
     * @param DOMXPath $xpath
     * @return array
     */
    #[NoDiscard]
    public static function extractStylesheets(DOMXPath $xpath): array
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:stylesheet");
        $stylesheets = [];
        foreach ($nodes as $node)
            $stylesheets[] = trim($node->textContent);
        return $stylesheets;
    }

    /**
     * Extract the font file names from an XPath object
     * - <lea:font>Comfortaa-Bold.ttf</lea:font>
     * - <lea:font>TeXGyreHeros-Regular.otf</lea:font>
     *
     * @param DOMXPath $xpath
     * @return array
     */
    #[NoDiscard]
    public static function extractFonts(DOMXPath $xpath): array
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:font");
        $fonts = [];
        foreach ($nodes as $node)
            $fonts[] = trim($node->textContent);
        return $fonts;
    }

    /**
     * Extract the ISBN from an XPath object
     * - ISBN-13 only. Not aware of any other formats going around for ebooks.
     * - <lea:isbn>987-1234567890</lea:isbn>
     *
     * @param DOMXPath $xpath
     * @return ISBN
     */
    #[NoDiscard]
    public static function extractISBN(DOMXPath $xpath): ISBN
    {
        return new ISBN(trim($xpath->evaluate(expression: "string(/" . self::$rootElement . "/lea:isbn)")));
    }

    /**
     * Extract the optional collection data from an XPath object
     * - <lea:collection>
     *     <lea:title>Logophilia</lea:title>
     *     <lea:type>origins@logophilia.eu</lea:type>
     *     <lea:position>origins@logophilia.eu</lea:position>
     *     <lea:issn>origins@logophilia.eu</lea:issn>
     *   </lea:collection>
     * This information is not mandatory, as not every book is part of a collection.
     * At the time of writing, only the collection type "series" is supported.
     * A "series" requires an ISSN.
     *
     * @param DOMXPath $xpath
     * @return Collection
     */
    #[NoDiscard]
    public static function extractCollection(DOMXPath $xpath): Collection
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:collection");
        if ($nodes === false || $nodes->length !== 1) return new Collection();
        $title = trim($xpath->evaluate(expression: "string(lea:title)", contextNode: $nodes[0]));
        $type = trim($xpath->evaluate(expression: "string(lea:type)", contextNode: $nodes[0]));
        $position = trim($xpath->evaluate(expression: "string(lea:position)", contextNode: $nodes[0]));
        $issn = trim($xpath->evaluate(expression: "string(lea:issn)", contextNode: $nodes[0]));
        return new Collection(title: $title, type: $type, position: $position, issn: $issn);
    }

    /**
     * Extract the texts from an XPath object
     * - File names are always relative to REPO
     * - Subfolder should be specified with the subfolder tag
     * - <lea:text>AboutTheAuthors.xhtml</lea:text>
     *
     * @param DOMXPath $xpath
     * @return array
     */
    #[NoDiscard]
    public static function extractTexts(DOMXPath $xpath): array
    {
        $nodes = $xpath->query(expression: "/" . self::$rootElement . "/lea:text");
        $texts = [];
        foreach ($nodes as $node)
            $texts[] = new Text(trim($node->textContent));
        return $texts;
    }

    /**
     * Extract the subfolder names from an XPath object.
     * - <lea:subfolder>tpsf-8</lea:subfolder>
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractSubFolder(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: "string(/" . self::$rootElement . "/lea:subfolder)"), "/ ");
    }

    /**
     * Extract the default caption from an XPath object.
     * - <lea:defaultcaption>illustration is in the public domain</lea:defaultcaption>
     *
     * @param DOMXPath $xpath
     * @return string
     */
    #[NoDiscard]
    public static function extractDefaultCaption(DOMXPath $xpath): string
    {
        return trim($xpath->evaluate(expression: "string(/" . self::$rootElement . "/lea:defaultcaption)"));
    }

    /**
     * Replace <lea:image> tags with xhtml template.
     *
     * @param Text $text
     * @param array $imageData
     * @return void
     */
    public static function replaceLeaImageTags(Text $text, array $imageData): void
    {
        $nodes = $text->xpath->query(expression: "//lea:image");
        foreach ($nodes as $node) {
            $fileName = trim($text->xpath->evaluate(expression: "string(lea:file)", contextNode: $node));
            $replacement = "<figure>"
                . "<img src='../Images/" . $imageData[$fileName]["file"] . "'/>"
                . "<figcaption>"
                . $imageData[$fileName]["caption"]
                . "</figcaption>"
                . "</figure>";
            $fragment = $text->dom->createDocumentFragment();
            $fragment->appendXML($replacement);
            $node->parentNode->replaceChild($fragment, $node);
        }
    }

    /**
     * Make-up
     * Make-up
     * Pink, blue
     * Purple, I wanna make it good for you
     *
     * Rewraps a passed DOMDocument into a standard ePub DOM document.
     *
     * @param DOMDocument $domDocument
     * @param string $title
     * @return DOMDocument
     * @throws DOMException
     */
    public static function reWrapDom(DOMDocument $domDocument, string $title = "TITLE"): DOMDocument
    {
        if (!str_contains($domDocument->saveXML(), XMLetsGoCrazy::$leaNamespace))
            return $domDocument;
        $impl = new DOMImplementation();
        $doctype = $impl->createDocumentType(qualifiedName: 'html'); // <!DOCTYPE html>
        $dom = $impl->createDocument(namespace: 'http://www.w3.org/1999/xhtml', qualifiedName: 'html', doctype: $doctype);
        $dom->encoding = 'UTF-8';
        $dom->formatOutput = false;
        $html = $dom->documentElement;
        $html->setAttributeNS(namespace: 'http://www.w3.org/2000/xmlns/', qualifiedName: 'xmlns:epub', value: 'http://www.idpf.org/2007/ops');
        $html->setAttribute(qualifiedName: 'lang', value: 'en-GB');
        $html->setAttributeNS(namespace: 'http://www.w3.org/XML/1998/namespace', qualifiedName: 'xml:lang', value: 'en-GB');
        $head = $dom->createElement('head');
        $html->appendChild($head);
        $generator = $dom->createElement('meta');
        $generator->setAttribute(qualifiedName: 'name', value: "generator");
        $generator->setAttribute(qualifiedName: 'content', value: Girlfriend::comeToMe()->leaNameShort);
        $head->appendChild($generator);
        $title = $dom->createElement('title', $title);
        $head->appendChild($title);
        $stylesheet = $dom->createElement('link');
        $stylesheet->setAttribute(qualifiedName: 'rel', value: "stylesheet");
        $stylesheet->setAttribute(qualifiedName: 'type', value: "text/css");
        $stylesheet->setAttribute(qualifiedName: 'href', value: "../Styles/stylesheet.css");
        $head->appendChild($stylesheet);
        $html->appendChild($head);
        $body = $dom->createElement('body');
        $body->setAttributeNS(namespace: 'http://www.idpf.org/2007/ops', qualifiedName: 'epub:type', value: 'bodymatter');
        $html->appendChild($body);
        $fragmentsRoot = $domDocument->documentElement;
        foreach ($fragmentsRoot->childNodes as $node)
            $body->appendChild($dom->importNode($node, deep: true));
        return $dom;
    }

    /**
     * Strip down, strip down
     * Elephants and flowers
     * Is everybody ready? Here we go
     *
     * Strips all Lea tags from a passed DOMDocument.
     *
     * @param DOMDocument $leaDom
     * @return DOMDocument
     * @throws DOMException
     */
    public static function stripLeaDom(DOMDocument $leaDom): DOMDocument
    {
        $xpath = self::createXPath($leaDom);
        $nodes = $xpath->query(expression: "//lea:*");
        foreach ($nodes as $node)
            $node->parentNode->removeChild($node);
        return $leaDom;
    }
}
