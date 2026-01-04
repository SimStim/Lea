<?php

declare(strict_types=1);

namespace Lea;

use NoDiscard;
use Lea\Domain\{Author, Ebook, ISBN, Text};

/**
 * There is a park that is known 4 the face it attracts. Admission is easy, just say U believe.
 */
final class PaisleyPark
{
    private string $xml {
        get => $this->xml ??= Girlfriend::comeToMe()->readFileOrDie(fileName: REPO . "/text/" . $this->fileName);
    }
    private(set) Ebook $ebook {
        get => $this->ebook ??= $this->cream();
    }

    public function __construct(
        private(set) string $fileName {
            set => trim(string: $value);
        }
    )
    {
    }

    /**
     * @return Ebook You got the horn so why don't you blow it?
     *
     * You got the horn so why don't you blow it?
     */
    #[NoDiscard]
    public function cream(): Ebook
    {
        return new EBook (fileName: $this->fileName);
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
