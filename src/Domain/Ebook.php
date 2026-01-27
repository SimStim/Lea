<?php

declare(strict_types=1);

namespace Lea\Domain;

use DOMDocument;
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
    private(set) DOMDocument $dom {
        get => $this->dom ??= XMLetsGoCrazy::createDOM($this->xml);
    }
    private(set) DOMXPath $xpath {
        get => $this->xpath ??= XMLetsGoCrazy::createXPath($this->dom);
    }
    private(set) string $title {
        get => $this->title ??= XMLetsGoCrazy::extractTitle($this->xpath);
    }
    private(set) string $description {
        get => $this->description ??= XMLetsGoCrazy::extractDescription($this->xpath);
    }
    private(set) Publisher $publisher {
        get => $this->publisher ??= XMLetsGoCrazy::extractPublisher($this->xpath);
    }
    private(set) string $rights {
        get => $this->rights ??= XMLetsGoCrazy::extractRights($this->xpath);
    }
    private(set) string $language {
        get => $this->language ??= XMLetsGoCrazy::extractLanguage($this->xpath);
    }
    private(set) array $authors {
        get => $this->authors ??= XMLetsGoCrazy::extractAuthors($this->xpath);
    }
    private(set) Date $date {
        get => $this->date ??= XMLetsGoCrazy::extractDate($this->xpath);
    }
    private(set) array $contributors {
        get => $this->contributors ??= XMLetsGoCrazy::extractContributors($this->xpath);
    }
    private(set) ISBN $isbn {
        get => $this->isbn ??= XMLetsGoCrazy::extractISBN($this->xpath);
    }
    private(set) Collection $collection {
        get => $this->collection ??= XMLetsGoCrazy::extractCollection($this->xpath);
    }
    private(set) array $texts {
        get => $this->texts ??= XMLetsGoCrazy::extractTexts($this->xpath);
    }
    private(set) array $subjects {
        get => $this->subjects ??= XMLetsGoCrazy::extractSubjects($this->xpath);
    }
    private(set) string $cover {
        get => $this->cover ??= XMLetsGoCrazy::extractCover($this->xpath);
    }
    private(set) array $images {
        get => $this->images ??= XMLetsGoCrazy::extractImages($this->xpath);
    }

    public function __construct(
        private(set) string $fileName {
            set => trim(string: $value);
        }
    )
    {
    }
}
