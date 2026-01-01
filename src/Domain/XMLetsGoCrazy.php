<?php

declare(strict_types=1);

namespace Lea\Domain;

use DOMDocument;
use DOMXPath;
use RuntimeException;

/**
 * XMLetsGoCrazy class for static domain helpers
 *
 * We're all excited, but we don't know why.
 */
final class XMLetsGoCrazy
{
    private static string $leaNamespace = "https://logophilia.eu/lea";

    public static function buildXPath(string $fragments): DOMXPath
    {
        $wrapped = "<xmletsgocrazy xmlns:lea='" . self::$leaNamespace . "'>$fragments</xmletsgocrazy>";
        $dom = new DOMDocument('1.0', 'UTF-8');
        if (!$dom->loadXML($wrapped, LIBXML_NONET))
            throw new RuntimeException("Malformed XML fragment");
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('lea', self::$leaNamespace);
        return $xpath;
    }
}

