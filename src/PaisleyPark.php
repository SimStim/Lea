<?php

declare(strict_types=1);

namespace Lea;

use NoDiscard;
use Throwable;
use Lea\Adore\DoveCry;
use Lea\Adore\Fancy;
use Lea\Adore\Flaw;
use Lea\Adore\Girlfriend;
use Lea\Domain\Author;
use Lea\Domain\Ebook;
use Lea\Domain\Text;
use Lea\Domain\XMLetsGoCrazy;

/**
 * There is a park that is known 4 the face it attracts. Admission is easy, just say U believe.
 */
final class PaisleyPark
{
    private(set) Ebook $ebook {
        get => $this->ebook ??= $this->cream($this->fileName);
    }
    private(set) TheOpera $theOpera {
        get => $this->theOpera ??= new TheOpera($this->ebook);
    }

    public function __construct(
        private string $fileName {
            set => trim(string: $value);
        }
    )
    {
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
     * I hope you don't mind I'm recording our conversati–
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
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
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
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
            return;
        }
        $subFolder = XMLetsGoCrazy::extractSubFolder($this->ebook->xpath);
        Girlfriend::comeToMe()->remember(name: "subfolder", data: $subFolder !== "" ? $subFolder . "/" : "");
        $defaultCaption = XMLetsGoCrazy::extractDefaultCaption($this->ebook->xpath);
        Girlfriend::comeToMe()->remember(name: "defaultcaption", data: $defaultCaption);
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
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there is more than one <lea:title> definitions.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:title")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "Multiple title tags defined in ebook." . PHP_EOL
                . "Until this is fixed, I will be using the first valid title found." . PHP_EOL
                . "Using title: '{$this->ebook->title}'",
                suggestion: "Edit the ebook's XML config file, making sure to use only one <lea:title> tag." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there is at least one <lea:description>.
         * - no = severe, continue
         */
        if ($this->ebook->description === "")
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "The description is not mandatory, but highly recommended.",
                suggestion: "Edit the ebook's XML config file, adding a single <lea:title> tag." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there is more than one <lea:description> definitions.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:description")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "Multiple description tags defined in ebook." . PHP_EOL
                . "Until this is fixed, I will be using the first valid description found." . PHP_EOL
                . "Using description: '{$this->ebook->description}'",
                suggestion: "Edit the ebook's XML config file, making sure to use only one <lea:description> tag." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there is at least one <lea:publisher>.
         * - no = severe, continue
         */
        if (!$this->ebook->publisher->isValid())
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "The publisher data is not mandatory, but highly recommended.",
                suggestion: "Edit the ebook's XML config file, adding a single <lea:publisher> tag." . PHP_EOL
                . "All publisher child tags are also mandatory: <lea:imprint>, and <lea:contact>." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there is at least one <lea:rights> tag.
         * - no = severe, continue
         */
        if ($this->ebook->rights === "")
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "The rights declaration is not mandatory, but highly recommended.",
                suggestion: "Edit the ebook's XML config file, adding a single <lea:rights> tag." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there is more than one <lea:rights> definitions.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:rights")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "Multiple rights tags defined in ebook." . PHP_EOL
                . "Until this is fixed, I will be using the first valid rights declaration." . PHP_EOL
                . "Using rights declaration: '{$this->ebook->rights}'",
                suggestion: "Edit the ebook's XML config file, making sure to use only one <lea:rights> tag." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there is at least one <lea:language> tag.
         * - no = severe, continue
         */
        if ($this->ebook->language === "")
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "The language declaration is not mandatory, but highly recommended.",
                suggestion: "Edit the ebook's XML config file, ascertaining a single <lea:language> tag." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
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
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
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
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if the date has been extracted successfully
         * - no = fatal
         */
        if (!$this->ebook->date->isValid())
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "The date is missing or invalid; possibly multiple date tags declared.",
                suggestion: "Edit the ebook's XML config file, ascertaining a single <lea:date> tag." . PHP_EOL
                . "All of date's child tags are mandatory: <lea:created>, <lea:modified>, and <lea:issued>." . PHP_EOL
                . "Check documentation if still unsure how to fix this." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there are invalid <contributor> definitions present in the ebook config.
         * - yes = continue with all valid <contributor> definitions
         */
        if (!empty($this->ebook->contributors)) {
            $reference = [];
            foreach ($this->ebook->contributors as $contributor)
                $reference[] = count($contributor->roles);
            if (!XMLetsGoCrazy::validateContributors(xpath: $this->ebook->xpath, reference: $reference))
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $this->ebook,
                    flaw: Flaw::Severe,
                    message: "Invalid contributor tag(s) detected in ebook." . PHP_EOL
                    . count($this->ebook->contributors) . " valid contributor(s) in total.",
                    suggestion: "Check the ebook's XML config file for all <lea:contributor> tags." . PHP_EOL
                    . "Most likely cause is a an error in one of the <lea:role> tags." . PHP_EOL
                    . "Also check documentation for permitted roles." . PHP_EOL
                    . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
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
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there are more than one <isbn> definitions.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:isbn")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "Multiple ISBN tags defined in ebook." . PHP_EOL
                . "Until this is fixed, I will be using the first valid ISBN found." . PHP_EOL
                . "Using ISBN: '{$this->ebook->isbn->isbn}'",
                suggestion: "Edit the ebook's XML config file, making sure to use only one <lea:isbn> tag." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Checks if there are any <lea:subject> declarations.
         * - no = warning about missing subject declarations
         */
        if (count($this->ebook->subjects) === 0)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Warning,
                message: "No subject declarations found for ebook.",
                suggestion: "Edit the ebook XML config file, adding <lea:subject> tags." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * If there is <lea:cover> declaration, check if the file exists in the file system.
         * - no = fatal
         */
        if (($this->ebook->cover !== "")
            && !is_file(filename: Girlfriend::$pathImages . Girlfriend::comeToMe()->recall(name: "subfolder") . $this->ebook->cover))
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "The cover file name was defined, but the file cannot be found in the file system." . PHP_EOL
                . "Cover file name: " . Girlfriend::$pathImages . Girlfriend::comeToMe()->recall(name: "subfolder") . $this->ebook->cover,
                suggestion: "Edit the ebook's XML config file, ascertaining the correct cover file name." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Check if there is more than one <lea:cover> definition.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:cover")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Severe,
                message: "Multiple cover tags defined in ebook." . PHP_EOL
                . "Until this is fixed, I will be using the first valid cover declaration." . PHP_EOL
                . "Using cover file name: '{$this->ebook->cover}'",
                suggestion: "Edit the ebook's XML config file, making sure to use only one <lea:cover> tag." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
        /**
         * Time to direct our gaze at the text files
         */
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
                    . "Text file name given: '"
                    . Girlfriend::$pathText . Girlfriend::comeToMe()->recall(name: "subfolder") . "$text->fileName'.",
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
                    . "Text file name given: '" . Girlfriend::$pathText . "$text->fileName'.",
                ));
                continue;
            }
            /**
             * Extract images and if successful, add images to the ebook image list.
             * - If unsuccessful: fatal
             */
            $images = XMLetsGoCrazy::extractImages($text->xpath);
            if ($images === false)
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: Flaw::Fatal,
                    message: "Missing or incomplete <lea:file> declaration within <lea:image>.",
                    suggestion: "Edit the text file, correcting any incomplete <lea:title> tags." . PHP_EOL
                    . "Text file name with error: '" . Girlfriend::$pathText . "$text->fileName'.",
                ));
            else $this->ebook->addImages(XMLetsGoCrazy::extractImages($text->xpath));
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
                    . "Text file name given: '" . Girlfriend::$pathText . "$text->fileName'.",
                ));
            /**
             * Checks if there are more than one <title> definitions.
             * - yes = use first, continue with warning message
             */
            if ($text->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:title")->length > 1)
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: Flaw::Severe,
                    message: "Multiple title tags defined in text." . PHP_EOL
                    . "Until this is fixed, I will be using the first valid title found." . PHP_EOL
                    . "Using title: '$text->title'",
                    suggestion: "Edit the text file, making sure to use only one <lea:title> tag." . PHP_EOL
                    . "Text file name given: '" . Girlfriend::$pathText . "$text->fileName'.",
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
                    . "Text file name given: '" . Girlfriend::$pathText . "$text->fileName'.",
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
                    . "Text file name given: '" . Girlfriend::$pathText . "$text->fileName'.",
                ));
            /**
             * Checks if there are more than one <lea:blurb> definitions.
             * - yes = use the first blurb found, continue with warning message
             */
            if ($text->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:blurb")->length > 1)
                Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                    domainObject: $text,
                    flaw: Flaw::Severe,
                    message: "Multiple blurbs found in text." . PHP_EOL
                    . "Continuing with the first blurb found." . PHP_EOL
                    . "Blurb I will be using: '$text->blurb'.",
                    suggestion: "Edit the text file, checking all <lea:blurb> tags." . PHP_EOL
                    . "Text file name given: '" . Girlfriend::$pathText . "$text->fileName'.",
                ));
        }
        /**
         * Checks if there are any <lea:image> tags defining file names not found in the file system.
         * - yes = fatal
         */
        $missing = [];
        foreach ($this->ebook->images as $image)
            if (!is_file(
                filename: Girlfriend::$pathImages . Girlfriend::comeToMe()->recall(name: "subfolder") . $image->fileName)
            )
                $missing[] = Girlfriend::$pathImages . Girlfriend::comeToMe()->recall(name: "subfolder") . $image->fileName;
        if (count($missing) > 0)
            Girlfriend::comeToMe()->makeDoveCry(new DoveCry(
                domainObject: $this->ebook,
                flaw: Flaw::Fatal,
                message: "Number of image tag(s) defined in ebook, but missing in file system: "
                . count($missing) . "." . PHP_EOL
                . "List of file name(s) declared but not found: " . PHP_EOL
                . implode(separator: PHP_EOL, array: $missing),
                suggestion: "Check the ebook's XML config file and all text files for missing or incorrect file names." . PHP_EOL
                . "Ebook XML config file name given: " . Girlfriend::$pathEbooks . "{$this->ebook->fileName}"
            ));
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
     * @param bool $keepCrying
     * @return bool
     */
    #[NoDiscard]
    public function inThisBedEyeScream(bool $keepCrying = false): bool
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
        if (!$keepCrying)
            Girlfriend::comeToMe()->silenceDoves();
        return !$fatal;
    }

    /**
     * You know, if you don't give me the real story, I'll have to make one up of my own.
     *
     * The Segue uses the established data clarity for a few final steps to put into place.
     *
     * @return void
     */
    public function seguePartTwo(): void
    {
        /**
         * Replace all <lea:image> tags with xhtml.
         */
        $imageData = [];
        foreach ($this->ebook->images as $image)
            $imageData[$image->fileName] = [
                "file" => Girlfriend::comeToMe()->strToEpubImageFileName($image->fileName),
                "caption" => $image->caption
            ];
        foreach ($this->ebook->texts as $text)
            XMLetsGoCrazy::replaceLeaImageTags($text, $imageData);
        /**
         * If a cover image was defined, create a text file for it and add it to the ePub.
         */
        if ($this->ebook->cover !== "") {
            $text = new Text(fileName: "cover.xhtml", xhtml: $this->theOpera->generateCoverFile(), title: "Cover");
            $text->addAuthor(new Author(Girlfriend::comeToMe()->leaNamePlain));
            $this->ebook->addText(text: $text);
        }
        /**
         * The nav.xhtml is mandatory ePub navigation, so we create and add it here.
         */
        $text = new Text(fileName: "nav.xhtml", xhtml: $this->theOpera->generateNavFile(), title: "ePub Navigation");
        $text->addAuthor(new Author(Girlfriend::comeToMe()->leaNamePlain));
        $this->ebook->addText(text: $text);
        /**
         * Let's also quickly ass all authors of text content to the Ebook authors list.
         * It'll be easier later, and any segue is supposed to provide smooth transitions.
         * We'll do it with an array, so we auto-eliminate duplicates.
         */
        $authors = [];
        foreach ($this->ebook->authors as $author)
            $authors[$author->name] = $author->fileAs;
        foreach ($this->ebook->texts as $text)
            foreach ($text->authors as $author)
                $authors[$author->name] = $author->fileAs;
        $this->ebook->eraseAuthors();
        foreach ($authors as $name => $fileAs)
            if ($name !== Girlfriend::comeToMe()->leaNamePlain)
                $this->ebook->addAuthor(new Author(name: $name, fileAs: $fileAs));
    }

    /**
     * With one more verse to the story
     * I need another piece of your ear
     * I want to hip you all to the reason
     * I'm known as the Player of the Year
     *
     * @return bool
     */
    public function pControl(): bool
    {
        $this->segue();
        if (!$this->inThisBedEyeScream()) exit;
        $this->seguePartTwo();
        if (!$this->inThisBedEyeScream()) exit;
        try {
            $return = $this->theOpera->conductor();
        } catch (Throwable $e) {
            Girlfriend::comeToMe()->extraordinary(throwable: $e);
        }
        echo sprintf("0 OK, %s:8%s", Girlfriend::comeToMe()->whereAmI()["line"], PHP_EOL);
        return $return;
    }
}
