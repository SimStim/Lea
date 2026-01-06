<?php

declare(strict_types=1);

namespace Lea;

use Lea\Domain\XMLetsGoCrazy;
use NoDiscard;
use Lea\Domain\Ebook;

/**
 * There is a park that is known 4 the face it attracts. Admission is easy, just say U believe.
 */
final class PaisleyPark
{
    private Ebook $ebook;

    public function __construct(string $fileName)
    {
        $this->ebook = $this->cream($fileName);
    }

    /**
     * You got the horn so why don't you blow it?
     *
     * @param string $fileName
     * @return Ebook
     */
    #[NoDiscard]
    private function cream(string $fileName): Ebook
    {
        return new Ebook(fileName: $fileName);
    }

    /**
     * You know, if you don't give me the real story, I'll have to make one up of my own.
     *
     * @return bool
     */
    #[NoDiscard]
    public function segue(): bool
    {
        /**
         * Checks if the ebook config file is well-formed.
         * - no = fatal
         */
        if (!XMLetsGoCrazy::isWellFormed($this->ebook->xpath))
            Girlfriend::comeToMe()->collectFallout("The content of the ebook config file is not well formed.");
        /**
         * Checks if there is at least one title.
         * - no = fatal
         */
        if ($this->ebook->title === "")
            Girlfriend::comeToMe()->collectFallout("The title is required");
        /**
         * Checks if there are more than one <title> definitions.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->evaluate(expression: 'count(//lea:title) > 1'))
            Girlfriend::comeToMe()->collectFallout("Multiple title tags defined in ebook file {$this->ebook->fileName}."
                . " Using first valid title found ('{$this->ebook->title}') until fixed.");
        /**
         * Checks if there is at least one author.
         * - no = fatal
         */
        if (count($this->ebook->authors) === 0)
            Girlfriend::comeToMe()->collectFallout("At least one author is required");
        /**
         * Checks if there are invalid <author> definitions present in the ebook config.
         * - yes = continue with all valid <author> definitions
         */
        if (!XMLetsGoCrazy::validateAuthors($this->ebook->xpath))
            Girlfriend::comeToMe()->collectFallout("Invalid author tag(s) detected in ebook file {$this->ebook->fileName}."
                . " Continuing with all (" . count($this->ebook->authors) . ") valid author definitions.");
        /**
         * Checks if the ISBN is valid.
         * - no = fatal
         */
        if (!$this->ebook->isbn->isWellFormed())
            Girlfriend::comeToMe()->collectFallout("The ISBN is invalid.");
        /**
         * Checks if there are more than one <isbn> definitions.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->evaluate(expression: 'count(//lea:isbn) > 1'))
            Girlfriend::comeToMe()->collectFallout("Multiple ISBNs defined in ebook file {$this->ebook->fileName}."
                . " Using first valid ISBN found ('{$this->ebook->isbn->isbn}') until fixed.");
        foreach ($this->ebook->texts as $text) {
            /**
             * Checks if the text file is well-formed.
             * - no = fatal
             */
            if (!XMLetsGoCrazy::isWellFormed($text->xpath))
                Girlfriend::comeToMe()->collectFallout("The content of the text file $text->fileName is not well formed.");
            /**
             * Checks if there is at least one title.
             * - no = fatal
             */
            if ($text->title === "")
                Girlfriend::comeToMe()->collectFallout("The title is required in text file $text->fileName.");
            /**
             * Checks if there are more than one <title> definitions.
             * - yes = use first, continue with warning message
             */
            if ($text->xpath->evaluate(expression: 'count(//lea:title) > 1'))
                Girlfriend::comeToMe()->collectFallout("Multiple titles defined in $text->fileName."
                    . " Using first ('$text->title') until fixed.");
            /**
             * Checks if there is at least one author.
             * - no = fatal
             */
            if (count($text->authors) === 0)
                Girlfriend::comeToMe()->collectFallout("At least one author is required in text file $text->fileName.");
            /**
             * Checks if there are invalid <author> definitions present in the text file.
             * - yes = continue with all valid <author> definitions
             */
            if (XMLetsGoCrazy::validateAuthors($text->xpath) === false)
                Girlfriend::comeToMe()->collectFallout("Invalid author tag(s) detected in text file $text->fileName."
                    . " Continuing with all (" . count($text->authors) . ") valid author definitions.");
            /**
             * Checks if there are more than one <blurb> definitions.
             * - yes = use the first blurb found, continue with warning message
             */
            if ($text->xpath->evaluate(expression: 'count(//lea:blurb) > 1'))
                Girlfriend::comeToMe()->collectFallout("Multiple blurbs found in text file $text->fileName."
                    . " Continuing with the first blurb found ('$text->blurb')");
        }
        return true;
    }

    /**
     * Ah, the opera.
     *
     * @return bool
     */
    #[NoDiscard]
    public function theOpera(): bool
    {
        return true;
    }
}
