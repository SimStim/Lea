<?php

declare(strict_types=1);

namespace Lea;

use NoDiscard;
use Lea\Adore\DoveCry;
use Lea\Adore\Fancy;
use Lea\Adore\Flaw;
use Lea\Adore\Girlfriend;
use Lea\Domain\Ebook;
use Lea\Domain\XMLetsGoCrazy;

/**
 * There is a park that is known 4 the face it attracts. Admission is easy, just say U believe.
 */
final class PaisleyPark
{
    private(set) Ebook $ebook;

    public function __construct(string $fileName)
    {
        $this->ebook = $this->cream($fileName);
    }

    /**
     * You got the horn so why don't you blow it?
     *
     * The Cream gets the hard data structures for the ebook all lubed up.
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
     * The Segue checks whether it is safe to proceed.
     *
     * @return void
     */
    public function segue(): void
    {
        /**
         * Checks if the ebook config file has been read successfully.
         * - no = fatal
         */
        if ($this->ebook->xml === "") {
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "The ebook XML config file could not be read.",
                suggestion: "Check for typos in the file name given." . PHP_EOL
                . "Ebook XML config file name given: {$this->ebook->fileName}"
            ));
            return;
        }
        /**
         * Checks if the ebook config file is well-formed.
         * - no = fatal
         */
        if (!XMLetsGoCrazy::isWellFormed($this->ebook->xpath)) {
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "The content of the ebook config file is not well formed.",
                suggestion: "Check the ebook's XML config file in your XML editor of choice." . PHP_EOL
                . "Ebook XML config file name: '{$this->ebook->fileName}'."
            ));
            return;
        }
        /**
         * Checks if there is at least one title.
         * - no = fatal
         */
        if ($this->ebook->title === "")
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "The title is required.",
                suggestion: "Edit the ebook's XML config file, adding a single <lea:title> tag." . PHP_EOL
                . "Ebook XML config file name: '{$this->ebook->fileName}'."
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
                suggestion: "Edit the ebook's XML config file, making sure to use only one <lea:title> tag." . PHP_EOL
                . "Ebook XML config file name: '{$this->ebook->fileName}'."
            ));
        /**
         * Checks if there is at least one author.
         * - no = fatal
         */
        if (count($this->ebook->authors) === 0)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "At least one author is required.",
                suggestion: "Edit the ebook's XML config file, adding at least one <lea:author> tag." . PHP_EOL
                . "Ebook XML config file name: '{$this->ebook->fileName}'."
            ));
        /**
         * Checks if there are invalid <author> definitions present in the ebook config.
         * - yes = continue with all valid <author> definitions
         */
        if (!XMLetsGoCrazy::validateAuthors($this->ebook->xpath))
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: (count($this->ebook->authors) === 0 ? Flaw::Fatal : Flaw::Severe),
                message: "Invalid author tag(s) detected in ebook." . PHP_EOL
                . count($this->ebook->authors) . " valid author definitions in total.",
                suggestion: "Edit the ebook's XML config file, checking all <lea:author> tags." . PHP_EOL
                . "Ebook XML config file name: '{$this->ebook->fileName}'."
            ));
        /**
         * Checks if there are invalid <contributor> definitions present in the ebook config.
         * - yes = continue with all valid <contributor> definitions
         */
        if (!empty($this->ebook->contributors)) {
            $reference = [];
            foreach ($this->ebook->contributors as $contributor)
                $reference[] = count($contributor->roles);
            if (!XMLetsGoCrazy::validateContributors($this->ebook->xpath, $reference))
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $this->ebook,
                    flaw: (count($this->ebook->authors) === 0 ? Flaw::Fatal : Flaw::Severe),
                    message: "Invalid contributor tag(s) detected in ebook." . PHP_EOL
                    . count($this->ebook->contributors) . " valid contributor(s) in total.",
                    suggestion: "Check the ebook's XML config file for all <lea:contributor> tags." . PHP_EOL
                    . "Most likely cause is a an error in a <lea:role>." . PHP_EOL
                    . "Check documentation for permitted roles." . PHP_EOL
                    . "Ebook XML config file name: '{$this->ebook->fileName}'."
                ));
        }
        /**
         * Checks if the ISBN is valid.
         * - no = fatal
         */
        if (!$this->ebook->isbn->isWellFormed())
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "The ISBN is invalid.",
                suggestion: "Edit the ebook's XML config file, checking the <lea:isbn> tag." . PHP_EOL
                . "Ebook XML config file name: '{$this->ebook->fileName}'."
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
                suggestion: "Edit the ebook's XML config file, making sure to use only one <lea:isbn> tag." . PHP_EOL
                . "Ebook XML config file name: '{$this->ebook->fileName}'."
            ));
        /**
         * Checks if there are any <lea:subject> declarations.
         * - no = warning about missing subject declarations
         */
        if (count($this->ebook->subjects) < 1)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Warning,
                message: "No Subject declarations found for ebook.",
                suggestion: "Edit the ebook XML config file, adding <lea:subject> tags." . PHP_EOL
                . "Ebook XML config file name: '{$this->ebook->fileName}'."
            ));
        foreach ($this->ebook->texts as $text) {
            /**
             * Checks if the text file has been read successfully.
             * - no = fatal
             */
            if ($text->xhtml === "") {
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: Flaw::Fatal,
                    message: "The text file could not be read.",
                    suggestion: "Check for typos in the file name given in the ebook XML config file." . PHP_EOL
                    . "Text file name given: '$text->fileName'",
                ));
                continue;
            }
            /**
             * Checks if the text file is well-formed.
             * - no = fatal
             */
            if (!XMLetsGoCrazy::isWellFormed($text->xpath)) {
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: Flaw::Fatal,
                    message: "The content of a text config file is not well formed.",
                    suggestion: "Check the text file in your XML editor of choice." . PHP_EOL
                    . "Text file name: '$text->fileName'",
                ));
                continue;
            }
            /**
             * Checks if there is at least one title.
             * - no = fatal
             */
            if ($text->title === "")
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: Flaw::Fatal,
                    message: "The title of the text is required.",
                    suggestion: "Edit the text file, adding a single <lea:title> tag." . PHP_EOL
                    . "Text file name: '$text->fileName'.",
                ));
            /**
             * Checks if there are more than one <title> definitions.
             * - yes = use first, continue with warning message
             */
            if ($text->xpath->evaluate(expression: 'count(//lea:title) > 1'))
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: Flaw::Severe,
                    message: "Multiple title tags defined in text." . PHP_EOL
                    . "Until this is fixed, I will be using the first valid title found." . PHP_EOL
                    . "Using title: '$text->title'",
                    suggestion: "Edit the text file, making sure to use only one <lea:title> tag." . PHP_EOL
                    . "Text file name: '$text->fileName'.",
                ));
            /**
             * Checks if there is at least one author.
             * - no = fatal
             */
            if (count($text->authors) === 0)
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: Flaw::Fatal,
                    message: "At least one author of the text is required.",
                    suggestion: "Edit the text file, adding at least one <lea:author> tag." . PHP_EOL
                    . "Text file name: '$text->fileName'.",
                ));
            /**
             * Checks if there are invalid <author> definitions present in the text file.
             * - yes = continue with all valid <author> definitions
             */
            if (XMLetsGoCrazy::validateAuthors($text->xpath) === false)
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: (count($text->authors) === 0 ? Flaw::Fatal : Flaw::Severe),
                    message: "Invalid author tag(s) detected in text." . PHP_EOL
                    . count($text->authors) . " valid author definitions in total.",
                    suggestion: "Edit the text file, checking all <lea:author> tags." . PHP_EOL
                    . "Text file name: '$text->fileName'.",
                ));
            /**
             * Checks if there are more than one <lea:blurb> definitions.
             * - yes = use the first blurb found, continue with warning message
             */
            if ($text->xpath->evaluate(expression: 'count(//lea:blurb) > 1'))
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: Flaw::Severe,
                    message: "Multiple blurbs found in text." . PHP_EOL
                    . "Continuing with the first blurb found." . PHP_EOL
                    . "Blurb I will be using: '$text->blurb'.",
                    suggestion: "Edit the text file, checking all <lea:blurb> tags." . PHP_EOL
                    . "Text file name: '$text->fileName'.",
                ));
        }
    }

    /**
     * Tell me, how're we gonna put this back together?
     * How're we gonna think with the same mind?
     *
     * If messages exist, display them, with additional info.
     *
     * Returns values:
     * - true if no fatal errors detected
     *
     * @return bool
     */
    #[NoDiscard]
    public function inThisBedEyeScream(): bool
    {
        $fatal = false;
        foreach (Girlfriend::comeToMe()->doveCries as $msg) {
            echo match ($msg->flaw) {
                Flaw::Info => Fancy::info(msg: "[ INFO ]") . PHP_EOL . $msg->message . PHP_EOL,
                Flaw::Warning => Fancy::warning(msg: "[ WARNING ]" . PHP_EOL) . $msg->message . PHP_EOL,
                Flaw::Severe => Fancy::severe(msg: "[ SEVERE ]") . PHP_EOL . $msg->message . PHP_EOL,
                Flaw::Fatal => Fancy::fatal(msg: "[ FATAL ]") . PHP_EOL . $msg->message . PHP_EOL
            };
            echo Fancy::suggestion(msg: "[ Suggestion ] ") . PHP_EOL . ($msg->suggestion ?: "[ none ]") . PHP_EOL . PHP_EOL;
            $fatal = $fatal || ($msg->flaw === Flaw::Fatal);
        }
        if (count(Girlfriend::comeToMe()->doveCries) !== 0) {
            echo Fancy::fatal(msg: "[ FATAL ]") . " cannot be resolved. No ePub will be produced." . PHP_EOL;
            echo Fancy::severe(msg: "[ SEVERE ]") . " requires guessing. The ePub must not be published." . PHP_EOL;
            echo Fancy::warning(msg: "[ WARNING ]") . " denotes missing optional data. The ePub should not be published." . PHP_EOL;
            echo Fancy::info(msg: "[ INFO ]") . " shows potential for improvement. The produced ePub may be less than ideal." . PHP_EOL;
        }
        return !$fatal;
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
