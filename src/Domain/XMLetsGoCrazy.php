<?php

declare(strict_types=1);

namespace Lea\Domain;

use DOMDocument;
use RuntimeException;

/**
 * XMLetsGoCrazy class for static domain helpers
 *
 * We're all excited, but we don't know why.
 */
final class XMLetsGoCrazy
{
    public static function extractSimpleTag(string $xhtml, string $tagName): array
    {
        $wrapped = "<?xml version='1.0' encoding='UTF-8'?><letsgocrazy>$xhtml</letsgocrazy>";
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        if (!$dom->loadXML($wrapped, LIBXML_NONET))
            throw new RuntimeException("Malformed XML fragment in xhtml source file.");
        $nodes = $dom->getElementsByTagName($tagName);
        $values = [];
        foreach ($nodes as $node)
            $values[] = trim($node->textContent);
        return $values;
        /**
         * for attributes (later maybe)
         *$values[] = [
         *          'content' => trim($node->textContent),
         *         'id'    => $node->getAttribute('id'),
         *    ];
         */
    }
}
