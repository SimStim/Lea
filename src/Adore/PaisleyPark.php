<?php

declare(strict_types=1);

namespace Lea\Adore;

use DOMException;
use Exception;
use NoDiscard;
use Throwable;
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
     * @throws Exception
     */
    public function segue(): void
    {
        /**
         * Checks if the ebook config file has been read successfully.
         * - no = fatal
         */
        if ($this->ebook->xml === "") {
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookReadError",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
            return;
        }
        /**
         * Checks if the ebook config file is well-formed.
         * - No = fatal
         */
        if (!XMLetsGoCrazy::isWellFormed($this->ebook->xpath)) {
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookNotWellFormed",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
            return;
        }
        $subFolder = XMLetsGoCrazy::extractSubFolder($this->ebook->xpath);
        Girlfriend::comeToMe()->remember(name: "subfolder", data: $subFolder !== "" ? $subFolder . "/" : "");
        $defaultCaption = XMLetsGoCrazy::extractDefaultCaption($this->ebook->xpath);
        Girlfriend::comeToMe()->remember(name: "defaultcaption", data: $defaultCaption);
        /**
         * Checks if there is at least one title.
         * - No = fatal
         */
        if ($this->ebook->title === "")
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookTitleRequired",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there is more than one <lea:title> definition.
         * - Yes = use first, continue with a warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:title")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookMultipleTitles",
                $this->ebook->title, Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there is at least one <lea:description>.
         * - No = severe, continue
         */
        if ($this->ebook->description === "")
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookDescriptionRecommended",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there is more than one <lea:description> definition.
         * - yes = use first, continue with a warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:description")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookMultipleDescriptions",
                $this->ebook->description, Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there is at least one <lea:publisher>.
         * - No = severe, continue
         */
        if (!$this->ebook->publisher->isValid())
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookPublisherRecommended",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there is at least one <lea:rights> tag.
         * - no = severe, continue
         */
        if ($this->ebook->rights === "")
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookRightsRecommended",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there is more than one <lea:rights> definitions.
         * - yes = use first, continue with warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:rights")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookMultipleRights",
                $this->ebook->rights, Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there is at least one <lea:language> tag.
         * - no = severe, continue
         */
        if ($this->ebook->language === "")
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookLanguageRecommended",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there is at least one author.
         * - no = fatal
         */
        if (count($this->ebook->authors) === 0)
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookAuthorRequired",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there are invalid <author> definitions present in the ebook config.
         * - yes = continue with all valid <author> definitions
         */
        if (!XMLetsGoCrazy::validateAuthors($this->ebook->xpath))
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookInvalidAuthors",
                count($this->ebook->authors), Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if the date has been extracted successfully
         * - No = fatal
         */
        if (!$this->ebook->date->isValid())
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookInvalidDate",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there are invalid <contributor> definitions present in the ebook config.
         * - Yes = continue with all valid <contributor> definitions
         */
        if (!empty($this->ebook->contributors)) {
            $reference = [];
            foreach ($this->ebook->contributors as $contributor)
                $reference[] = count($contributor->roles);
            if (!XMLetsGoCrazy::validateContributors(xpath: $this->ebook->xpath, reference: $reference))
                Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookInvalidContributor",
                    count($this->ebook->contributors), Girlfriend::$pathEbooks . $this->ebook->fileName);
        }
        /**
         * Checks if the ISBN is valid.
         * - No = fatal
         */
        if (!$this->ebook->isbn->isValid)
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookInvalidISBN",
                $this->ebook->isbn->isbn, Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there are more than one <isbn> definitions.
         * - Yes = use first, continue with a warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:isbn")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookMultipleISBNs",
                $this->ebook->isbn->isbn, Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Checks if there are any <lea:subject> declarations.
         * - no = warning about missing subject declarations
         */
        if (count($this->ebook->subjects) === 0)
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookSubjectRecommended",
                Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * If there is a <lea:cover> declaration, check if the file exists in the file system.
         * - No = fatal
         */
        $coverFileName = Girlfriend::$pathImages . Girlfriend::comeToMe()->recall(name: "subfolder") . $this->ebook->cover;
        if (($this->ebook->cover !== "") && !is_file(filename: $coverFileName))
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookInvalidCover",
                $coverFileName, Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Check if there is more than one <lea:cover> definition.
         * - Yes = use first, continue with a warning message
         */
        if ($this->ebook->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:cover")->length > 1)
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "ebookMultipleCovers",
                $this->ebook->cover, Girlfriend::$pathEbooks . $this->ebook->fileName);
        /**
         * Time to direct our gaze at the text files
         */
        foreach ($this->ebook->texts as $text) {
            /**
             * Checks if the text file has been read successfully.
             * - No = fatal
             */
            if ($text->xhtml === "") {
                Girlfriend::comeToMe()->makeDoveCry($text, "textReadError",
                    Girlfriend::$pathText . Girlfriend::comeToMe()->recall(name: "subfolder") . "$text->fileName");
                continue;
            }
            /**
             * Checks if the text file is well-formed.
             * - No = fatal
             */
            if (!XMLetsGoCrazy::isWellFormed($text->xpath)) {
                Girlfriend::comeToMe()->makeDoveCry($text, "textNotWellFormed",
                    Girlfriend::$pathText . Girlfriend::comeToMe()->recall(name: "subfolder") . "$text->fileName");
                continue;
            }
            /**
             * Extract images from text content files and add them to the ebook image list.
             */
            $this->ebook->addImages(XMLetsGoCrazy::extractImages($text->xpath));
            /**
             * Checks if there is at least one title.
             * - No = fatal
             */
            if ($text->title === "")
                Girlfriend::comeToMe()->makeDoveCry($text, "textTitleRequired",
                    Girlfriend::$pathText . Girlfriend::comeToMe()->recall(name: "subfolder") . "$text->fileName");
            /**
             * Checks if there are more than one <title> definitions.
             * - Yes = use first, continue with a warning message
             */
            if ($text->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:title")->length > 1)
                Girlfriend::comeToMe()->makeDoveCry($text, "textMultipleTitles", $text->title,
                    Girlfriend::$pathText . Girlfriend::comeToMe()->recall(name: "subfolder") . "$text->fileName");
            /**
             * Checks if there is at least one author.
             * - no = fatal
             */
            if (count($text->authors) === 0)
                Girlfriend::comeToMe()->makeDoveCry($text, "textAuthorRequired",
                    Girlfriend::$pathText . Girlfriend::comeToMe()->recall(name: "subfolder") . "$text->fileName");
            /**
             * Checks if there are invalid <author> definitions present in the text file.
             * - Yes = continue with all valid <author> definitions
             */
            if (XMLetsGoCrazy::validateAuthors($text->xpath) === false)
                Girlfriend::comeToMe()->makeDoveCry($text, "textInvalidAuthors", count($text->authors),
                    Girlfriend::$pathText . Girlfriend::comeToMe()->recall(name: "subfolder") . "$text->fileName");
            /**
             * Checks if there are more than one <lea:blurb> definitions.
             * - Yes = use the first blurb found, continue with a warning message
             */
            if ($text->xpath->query(expression: "/" . XMLetsGoCrazy::$rootElement . "/lea:blurb")->length > 1)
                Girlfriend::comeToMe()->makeDoveCry($text, "textMultipleBlurbs", $text->blurb,
                    Girlfriend::$pathText . Girlfriend::comeToMe()->recall(name: "subfolder") . "$text->fileName");
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
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "imageReadError",
                count($missing), implode(separator: PHP_EOL, array: $missing),
                Girlfriend::$pathEbooks . $this->ebook->fileName);
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
        $infoOnly = true;
        foreach (Girlfriend::comeToMe()->doveCries as $msg) {
            echo match ($msg->flaw) {
                Flaw::Info => Fancy::info(msg: "[ INFO ]") . PHP_EOL . $msg->message . PHP_EOL,
                Flaw::Warning => Fancy::warning(msg: "[ WARNING ]" . PHP_EOL) . $msg->message . PHP_EOL,
                Flaw::Severe => Fancy::severe(msg: "[ SEVERE ]") . PHP_EOL . $msg->message . PHP_EOL,
                Flaw::Fatal => Fancy::fatal(msg: "[ FATAL ]") . PHP_EOL . $msg->message . PHP_EOL
            };
            echo Fancy::suggestion(msg: "[ Suggestion ] ") . PHP_EOL . ($msg->suggestion ?: "[ none ]") . PHP_EOL . PHP_EOL;
            $fatal = $fatal || ($msg->flaw === Flaw::Fatal);
            $infoOnly = $infoOnly && ($msg->flaw === Flaw::Info);
        }
        if (!$infoOnly) {
            echo Fancy::fatal(msg: "[ FATAL ]") . " cannot be resolved. No ePub will be produced." . PHP_EOL;
            echo Fancy::severe(msg: "[ SEVERE ]") . " requires guessing. The ePub must not be published." . PHP_EOL;
            echo Fancy::warning(msg: "[ WARNING ]") . " denotes missing optional data. The ePub should not be published." . PHP_EOL;
            echo Fancy::info(msg: "[ INFO ]") . " shows potential for improvement. The produced ePub may be less than ideal." . PHP_EOL;
        }
        echo PHP_EOL;
        return !$fatal;
    }

    /**
     * Validate external links in the text files.
     *
     * @return void
     * @throws Exception
     */
    private function validateUrls(): void
    {
        $urls = [];
        foreach ($this->ebook->texts as $text)
            foreach (XMLetsGoCrazy::extractLinks($text->xpath) as $link)
                if (filter_var($link, filter: FILTER_VALIDATE_URL) !== false)
                    $urls[$link] = $link;
        $animCtr = 0;
        $handles = [];
        $mh = curl_multi_init();
        foreach ($urls as $url) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => false,            // GET request, because Elon Musk sucks (sometimes)
                CURLOPT_RANGE => '0-0',
                CURLOPT_TIMEOUT => 5,               // Total timeout: 5 seconds max for the whole request
                CURLOPT_CONNECTTIMEOUT => 3,        // Max 3 seconds to establish connection
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_USERAGENT => Girlfriend::comeToMe()->leaNamePlain,
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[$url] = $ch;                   // keep reference to the curl handle
        }
        $prevActive = count($handles);
        echo Fancy::HIDE_CURSOR;
        do {
            curl_multi_exec($mh, still_running: $active);
            curl_multi_select($mh);
            if ($active !== $prevActive)
                echo "\r[ " . Fancy::PURPLE_RAIN
                    . Fancy::ANIMATION[$animCtr++ % strlen(string: Fancy::ANIMATION)] . Fancy::RESET
                    . " ] [ " . Fancy::INVERSE . $active . "/" . count($handles) . " active" . Fancy::RESET . " ]"
                    . " Resolving external link '" . array_rand($urls) . "'" . Fancy::CLR_EOL;
            $prevActive = $active;
        } while ($active > 0);
        echo Fancy::UNHIDE_CURSOR;
        foreach ($handles as $key => $ch) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            if ($httpCode >= 400)
                Girlfriend::comeToMe()->makeDoveCry($this->ebook, "externalLinkCheckFailed", $key);
            elseif ($error)
                Girlfriend::comeToMe()->makeDoveCry($this->ebook, "externalLinkCheckTimeout", $key, $error);
        }
        curl_multi_close($mh);
        echo "\r" . Fancy::CLR_EOL;
    }

    /**
     * You know, if you don't give me the real story, I'll have to make one up of my own.
     *
     * The Segue uses the established data clarity for a few final steps to put into place.
     *
     * @return void
     * @throws DOMException
     * @throws Exception
     */
    public function seguePartTwo(): void
    {
        /**
         * Replace all <lea:image> tags with xhtml.
         */
        $imageData = [];
        foreach ($this->ebook->images as $image)
            $imageData[] = $image->fileName;
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
         * Let's also quickly add all authors of text content to the Ebook authors list.
         * It'll be easier later, and any segue is supposed to provide smooth transitions.
         * We'll do it with an array, so we auto-eliminate duplicates.
         */
        $authorData = [];
        foreach ($this->ebook->authors as $author)
            $authorData[$author->name] = $author->fileAs;
        foreach ($this->ebook->texts as $text)
            foreach ($text->authors as $author)
                $authorData[$author->name] = $author->fileAs;
        $this->ebook->eraseAuthors();
        foreach ($authorData as $name => $fileAs)
            if ($name !== Girlfriend::comeToMe()->leaNamePlain)
                $this->ebook->addAuthor(new Author(name: $name, fileAs: $fileAs));
        /**
         * If the user requested to check external links, do it now.
         */
        if (Girlfriend::comeToMe()->recall(name: "check-links") === "yes")
            $this->validateUrls();
        else
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "linksNotChecked", $this->ebook->fileName);
        /**
         * At this point, we want to resolve any links in the text content.
         */
        $targetData = [];
        foreach ($this->ebook->targets as $target)
            $targetData[$target->identifier] = [
                "name" => $target->name,
                "identifier" => $target->identifier,
                "targetFileName" => $target->targetFileName
            ];
        foreach ($this->ebook->texts as $text) {
            XMLetsGoCrazy::replaceLeaTargetTags($text);
            XMLetsGoCrazy::replaceLeaLinkTags($text, $targetData);
        }
        /**
         * Finally, throw an Info message to the user if EPUBCheck was not requested.
         */
        if (Girlfriend::comeToMe()->recall(name: "check-epub") === "no")
            Girlfriend::comeToMe()->makeDoveCry($this->ebook, "epubNotChecked", $this->ebook->fileName);
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
        try {
            $this->theOpera->theOverture(); // ascertain we're laughing in the purple rain
            $this->segue();
            if (!$this->inThisBedEyeScream()) exit;
            $errorLog = Girlfriend::comeToMe()->doveCries;
            Girlfriend::comeToMe()->silenceDoves();
            $this->seguePartTwo();
            if (!$this->inThisBedEyeScream()) exit;
            $errorLog = array_merge($errorLog, Girlfriend::comeToMe()->doveCries);
            Girlfriend::comeToMe()->silenceDoves();
            $return = $this->theOpera->conductor($errorLog);
        } catch (Throwable $e) {
            Girlfriend::comeToMe()->extraordinary(throwable: $e);
        }
        echo sprintf("0 OK, %s:8%s", Girlfriend::comeToMe()->whereAmI()["line"], PHP_EOL);
        return $return;
    }
}
