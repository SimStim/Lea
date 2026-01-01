<?php

declare(strict_types=1);

namespace Lea\Domain;

use DOMXPath;
use Lea\Girlfriend;
use RuntimeException;

/**
 * Text domain class
 */
final class Text
{
    private(set) string $title {
        get => $this->title ??= $this->extractTitle();
    }
    private(set) Author|array $author {
        get => $this->author ??= $this->extractAuthor();
    }
    private(set) string $blurb {
        get => $this->blurb ??= $this->extractBlurb();
    }
    private string $xhtml {
        get => $this->xhtml ??= Girlfriend::comeToMe()->readFileOrDie(fileName: REPO . "/text/" . $this->fileName);
    }
    private DOMXPath $xpath {
        get => $this->xpath ??= XMLetsGoCrazy::buildXPath($this->xhtml);
    }

    public function __construct(
        private(set) string $fileName {
            set => trim(string: $value);
        }
    )
    {
        $this->validateTextOrDie();
    }

    private function extractTitle(): string
    {
        return trim($this->xpath->evaluate('string(//lea:title)'));
    }

    private function extractAuthor(): array
    {
        $nodes = $this->xpath->query('//lea:author');
        if ($nodes->length === 0) return [];
        $authors = [];
        foreach ($nodes as $node) {
            $name = trim($this->xpath->evaluate('string(lea:name)', $node));
            if ($name === '') {
                Girlfriend::comeToMe()->collectFallout("Detected an invalid author tag in file $this->fileName.");
                continue;
            }
            $fileAs = trim($this->xpath->evaluate('string(lea:file-as)', $node));
            $authors[] = new Author($name, $fileAs);
        }
        return $authors;
    }

    private function extractBlurb(): string
    {
        return trim($this->xpath->evaluate('string(//lea:blurb)'));
    }

    private function validateTextOrDie(): void
    {
        if ($this->title === "")
            throw new RuntimeException("The title is required");
        if (is_array($this->author) && count($this->author) === 0)
            throw new RuntimeException("At least one author is required");
    }
}
