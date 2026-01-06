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
        get => $this->xhtml ??= Girlfriend::comeToMe()->readFile(fileName: Girlfriend::$pathText . $this->fileName);
    }
    private(set) DOMXPath $xpath {
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
    }
}
