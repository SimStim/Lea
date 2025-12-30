<?php

declare(strict_types=1);

namespace Lea;

use NoDiscard;
use Lea\Domain\{Author, Ebook, ISBN, Text};

// There is a park that is known 4 the face it attracts. Admission is easy, just say U believe.

final class PaisleyPark
{
    private(set) Ebook $ebook {
        get => $this->ebook ??= new Ebook();
    }

    public function __construct(
        private(set) string $ebookConfigFile = "" {
            set => trim(string: $value);
        }
    )
    {
    }

    /**
     * @param string $ebookConfigFile
     * @return true|array
     *
     * You got the horn so why don't you blow it?
     */
    #[NoDiscard]
    public function cream(string $ebookConfigFile = ""): true|array
    {
        if ($ebookConfigFile !== "") $this->ebookConfigFile = $ebookConfigFile;
        $this->ebook->title = "Hubris";
        $this->ebook->author = new Author(name: "Idoru Toei", fileAs: "Pech, Eduard [Idoru Toei]");
        $this->ebook->isbn = new ISBN(isbn: "978-9908972633");
        $this->ebook->addText(text: new Text(title: "Hubris chapter 1", author: new Author(name: "Idoru Toei", fileAs: "Eduard Pech"), blurb: "<p>We stared into the void.</p>"));
        return true;
    }

    /**
     * @return true|array
     *
     * You know, if you don't give me the real story, I'll have to make one up of my own.
     */
    #[NoDiscard]
    public function segue(): true|array
    {
        return true;
    }

    /**
     * @return true|array
     *
     * Ah, the opera.
     */
    #[NoDiscard]
    public function theOpera(): true|array
    {
        return true;
    }
}
