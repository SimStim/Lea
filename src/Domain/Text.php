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
    private string $xhtml {
        get => $this->xhtml ??= Girlfriend::comeToMe()->readFileOrDie(fileName: REPO . "/text/" . $this->fileName);
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
    private(set) string $blurb {
        get => $this->blurb ??= XMLetsGoCrazy::extractBlurb($this->xpath);
    }

    public function __construct(
        private(set) string $fileName {
            set => trim(string: $value);
        }
    )
    {
        $this->validateTextOrDie();
    }

    /**
     * Check if Text object is valid:
     * - mandatory information is present
     *
     * @return void
     */
    private function validateTextOrDie(): void
    {
        if ($this->title === "")
            throw new RuntimeException(message: "The title is required");
        if (count($this->authors) === 0)
            throw new RuntimeException(message: "At least one author is required");
    }
}
