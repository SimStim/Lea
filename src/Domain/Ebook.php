<?php

declare(strict_types=1);

namespace Lea\Domain;

use DOMXPath;
use Lea\Girlfriend;
use RuntimeException;

/**
 * Ebook domain class
 */
final class Ebook
{
    private string $xhtml {
        get => $this->xhtml ??= Girlfriend::comeToMe()->readFileOrDie(fileName: REPO . "/configs/ebooks//" . $this->fileName);
    }
    private DOMXPath $xpath {
        get => $this->xpath ??= XMLetsGoCrazy::buildXPath($this->xhtml, $this->fileName);
    }
    private(set) string $title {
        get => $this->title ??= XMLetsGoCrazy::extractTitle($this->xpath, $this->fileName);
    }
    private(set) array $authors {
        get => $this->authors ??= XMLetsGoCrazy::extractAuthors($this->xpath, $this->fileName);
    }
    private(set) ISBN $isbn {
        get => $this->isbn ??= new ISBN(XMLetsGoCrazy::extractISBN($this->xpath));
    }
    private(set) array $texts {
        get => $this->texts ??= XMLetsGoCrazy::extractTexts($this->xpath);
    }

    public function __construct(
        private(set) string $fileName {
            set => trim(string: $value);
        }
    )
    {
        $this->validateEbookOrDie();
    }

    /**
     * Check if Text object is valid:
     * - mandatory information is present
     *
     * @return void
     */
    private function validateEbookOrDie(): void
    {
        if ($this->title === "")
            throw new RuntimeException(message: "The title is required");
        if (count($this->authors) === 0)
            throw new RuntimeException(message: "At least one author is required");
    }
}
