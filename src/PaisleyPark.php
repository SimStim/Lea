<?php

declare(strict_types=1);

namespace Lea;

use NoDiscard;
use Lea\Adore\DoveCry;
use Lea\Adore\Flaw;
use Lea\Adore\Girlfriend;
use Lea\Domain\Ebook;
use Lea\Domain\XMLetsGoCrazy;

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
     * @return void
     */
    public function segue(): void
    {
        /**
         * Checks if the ebook config file is well-formed.
         * - no = fatal
         */
        if (!XMLetsGoCrazy::isWellFormed($this->ebook->xpath))
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "The content of the ebook config file is not well formed.",
                suggestion: "Check the ebook's XML config file in your XML editor of choice."
            ));
        /**
         * Checks if there is at least one title.
         * - no = fatal
         */
        if ($this->ebook->title === "")
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "The title is required",
                suggestion: "Edit the ebook's XML config file, adding a single <lea:title> tag."
            ));
        /**
         * Checks if there are more than one <title> definitions.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->evaluate(expression: 'count(//lea:title) > 1'))
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "Multiple title tags defined in ebook." . PHP_EOL
                . "Until this is fixed, I will be using the first valid title found." . PHP_EOL
                . "Using title: '{$this->ebook->title}'",
                suggestion: "Edit the ebook's XML config file, making sure to use only one <lea:title> tag."
            ));
        /**
         * Checks if there is at least one author.
         * - no = fatal
         */
        if (count($this->ebook->authors) === 0)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "At least one author is required",
                suggestion: "Edit the ebook's XML config file, adding at least one <lea:author> tag."
            ));
        /**
         * Checks if there are invalid <author> definitions present in the ebook config.
         * - yes = continue with all valid <author> definitions
         */
        if (!XMLetsGoCrazy::validateAuthors($this->ebook->xpath))
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "Invalid author tag(s) detected in ebook file {$this->ebook->fileName}." . PHP_EOL
                . "Continuing with all (" . count($this->ebook->authors) . ") valid author definitions.",
                suggestion: "Edit the ebook's XML config file, checking all <lea:author> tags."
            ));
        /**
         * Checks if the ISBN is valid.
         * - no = fatal
         */
        if (!$this->ebook->isbn->isWellFormed())
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "The ISBN is invalid.",
                suggestion: "Edit the ebook's XML config file, checking the <lea:isbn> tag."
            ));
        /**
         * Checks if there are more than one <isbn> definitions.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->evaluate(expression: 'count(//lea:isbn) > 1'))
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "Multiple ISBN tags defined in ebook." . PHP_EOL
                . "Until this is fixed, I will be using the first valid ISBN found." . PHP_EOL
                . "Using ISBN: '{$this->ebook->isbn->isbn}'",
                suggestion: "Edit the ebook's XML config file, making sure to use only one <lea:isbn> tag."
            ));
        foreach ($this->ebook->texts as $text) {
            /**
             * Checks if the text file is well-formed.
             * - no = fatal
             */
            if (!XMLetsGoCrazy::isWellFormed($text->xpath))
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $this->ebook,
                    flaw: Flaw::Fatal,
                    message: "The content of a text config file is not well formed." . PHP_EOL
                    . "Text file name: '$text->fileName'",
                    suggestion: "Check the text file in your XML editor of choice."
                ));
            /**
             * Checks if there is at least one title.
             * - no = fatal
             */
            if ($text->title === "")
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $this->ebook,
                    flaw: Flaw::Fatal,
                    message: "The title of the text is required." . PHP_EOL
                    . "Text file name: '$text->fileName'.",
                    suggestion: "Edit the text file, adding a single <lea:title> tag."
                ));
            /**
             * Checks if there are more than one <title> definitions.
             * - yes = use first, continue with warning message
             */
            if ($text->xpath->evaluate(expression: 'count(//lea:title) > 1'))
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $this->ebook,
                    flaw: Flaw::Severe,
                    message: "Multiple title tags defined in text." . PHP_EOL
                    . "Until this is fixed, I will be using the first valid title found." . PHP_EOL
                    . "Using title: '{$this->ebook->title}'" . PHP_EOL
                    . "Text file name: '$text->fileName'.",
                    suggestion: "Edit the text file, making sure to use only one <lea:title> tag.",
                ));
            /**
             * Checks if there is at least one author.
             * - no = fatal
             */
            if (count($text->authors) === 0)
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $this->ebook,
                    flaw: Flaw::Fatal,
                    message: "At least one author of the text is required." . PHP_EOL
                    . "Text file name: '$text->fileName'.",
                    suggestion: "Edit the text file, adding at least one <lea:author> tag."
                ));
            /**
             * Checks if there are invalid <author> definitions present in the text file.
             * - yes = continue with all valid <author> definitions
             */
            if (XMLetsGoCrazy::validateAuthors($text->xpath) === false)
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $this->ebook,
                    flaw: Flaw::Severe,
                    message: "Invalid author tag(s) detected in text." . PHP_EOL
                    . "Text file $text->fileName." . PHP_EOL
                    . "Continuing with all (" . count($this->ebook->authors) . ") valid author definitions.",
                    suggestion: "Edit the text file, checking all <lea:author> tags."
                ));
            /**
             * Checks if there are more than one <blurb> definitions.
             * - yes = use the first blurb found, continue with warning message
             */
            if ($text->xpath->evaluate(expression: 'count(//lea:blurb) > 1'))
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $this->ebook,
                    flaw: Flaw::Severe,
                    message: "Multiple blurbs found in text." . PHP_EOL
                    . "Text file $text->fileName." . PHP_EOL
                    . "Continuing with the first blurb found." . PHP_EOL
                    . "Blurb I will be using: '$text->blurb'.",
                    suggestion: "Edit the text file, checking all <lea:blurb> tags."
                ));
        }
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
