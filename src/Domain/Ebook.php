<?php

declare(strict_types=1);

namespace Lea\Domain;

use DOMXPath;
use Lea\Adore\Girlfriend;

/**
 * Ebook domain class
 */
final class Ebook
{
    private(set) string $xml {
        get => $this->xml ??= Girlfriend::comeToMe()->readFile(fileName: Girlfriend::$pathEbooks . $this->fileName);
    }
    private(set) DOMXPath $xpath {
        get => $this->xpath ??= XMLetsGoCrazy::buildXPath($this->xml);
    }
    private(set) string $title {
        get => $this->title ??= XMLetsGoCrazy::extractTitle($this->xpath);
    }
    private(set) array $authors {
        get => $this->authors ??= XMLetsGoCrazy::extractAuthors($this->xpath);
    }
    private(set) array $contributors {
        get => $this->contributors ??= XMLetsGoCrazy::extractContributors($this->xpath);
    }
    private(set) ISBN $isbn {
        get => $this->isbn ??= new ISBN(XMLetsGoCrazy::extractISBN($this->xpath));
    }
    private(set) array $texts {
        get => $this->texts ??= XMLetsGoCrazy::extractTexts($this->xpath);
    }
    private(set) array $subjects {
        get => $this->subjects ??= XMLetsGoCrazy::extractSubjects($this->xpath);
    }

    public function __construct(
        private(set) string $fileName {
            set => trim(string: $value);
        }
    )
    {
    }
}
