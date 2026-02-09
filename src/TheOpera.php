<?php

declare(strict_types=1);

namespace Lea;

use NoDiscard;
use DOMException;
use ZipArchive;
use Lea\Adore\Girlfriend;
use Lea\Domain\Ebook;
use Lea\Domain\XMLetsGoCrazy;

final class TheOpera
{
    private array $identifiers {
        get => $this->identifiers ??= $this->compileIdentifiers();
    }
    private array $idMarkers = [
        "text" => "lea:text:",
        "author" => "lea:creator:",
        "contributor" => "lea:contributor:",
        "font" => "lea:font:",
        "image" => "lea:image:",
        "stylesheet" => "lea:stylesheet:",
    ];

    public function __construct(private(set) readonly Ebook $ebook)
    {
    }

    /**
     * The Overture:
     * - Happens before the opera
     * - Sets the themes
     * - Establishes consequences
     * - Tells the audience how to listen
     *
     * @return void (also known as Caesura)
     */
    private function theOverture(): void
    {
        $hashes = [
            "PurpleRain.txt" => "247e5c56d2619ee9d29c4c56d69cacf917b49a572696ea60ba742d365b983112",
            "mimetype" => "e468e350d1143eb648f60c7b0bd6031101ec0544a361ca74ecef256ac901f48b",
            "container.xml" => "c54cb884813a53ce2fc9b3102ca8ee5c03b0397a2cb984500830e86c65ec092f",
            "covertemplate.xhtml" => "2d3d15c1277cc6a1f429afb8ef8dcc8e04949ccc4f743c4039333245ca7f76ce",
            "navtemplate.xhtml" => "d11d6254bd0701633e39f92211d512af19fbb62d270bb0ab460be3f684456a38",
        ];
        foreach ($hashes as $fileName => $hash) {
            $content = Girlfriend::comeToMe()->readFile(fileName: Girlfriend::$pathPurpleRain . $fileName);
            if (hash(algo: "sha256", data: $content) !== $hash) {
                echo($fileName === array_key_first($hashes)
                    ? "Die weißen Tauben sind müde." . PHP_EOL
                    : Girlfriend::comeToMe()->readFile(fileName: Girlfriend::$pathPurpleRain . array_key_first($hashes)));
                exit;
            }
        }
    }

    /**
     * Build normalized identifiers for all relevant objects.
     *
     * @return array
     */
    private function compileIdentifiers(): array
    {
        $identifiers = [];
        foreach ($this->ebook->authors as $author) {
            $identifiers[$this->idMarkers["author"] . $author->name] = [
                "epubIdentifier" => "lea-cre-" . Girlfriend::comeToMe()->strToEpubIdentifier($author->name),
                "epubFileName" => "",
            ];
        }
        foreach ($this->ebook->contributors as $contributor) {
            $identifiers[$this->idMarkers["contributor"] . $contributor->name] = [
                "epubIdentifier" => "lea-con-" . Girlfriend::comeToMe()->strToEpubIdentifier($contributor->name),
                "epubFileName" => "",
            ];
        }
        foreach ($this->ebook->texts as $text) {
            $title = $text->title . " by " . $text->authors[0]->name;
            $identifiers[$this->idMarkers["text"] . $text->fileName] = [
                "epubIdentifier" => "lea-txt-" . Girlfriend::comeToMe()->strToEpubIdentifier($title),
                "epubFileName" => Girlfriend::comeToMe()->strToEpubTextFileName($title),
            ];
            foreach ($text->authors as $author) {
                $identifiers[$this->idMarkers["author"] . $author->name] = [
                    "epubIdentifier" => "lea-cre-" . Girlfriend::comeToMe()->strToEpubIdentifier($author->name),
                    "epubFileName" => "",
                ];
            }
        }
        $identifiers[$this->idMarkers["text"] . "nav.xhtml"] = [
            "epubIdentifier" => "lea-txt-nav-xhtml",
            "epubFileName" => "nav.xhtml",
        ];
        foreach ($this->ebook->fonts as $font) {
            $identifiers[$this->idMarkers["font"] . $font] = [
                "epubIdentifier" => "lea-fnt-" . Girlfriend::comeToMe()->strToEpubIdentifier($font),
                "epubFileName" => basename($font),
            ];
        }
        foreach ($this->ebook->stylesheets as $stylesheet) {
            $identifiers[$this->idMarkers["stylesheet"] . $stylesheet] = [
                "epubIdentifier" => "lea-css-" . Girlfriend::comeToMe()->strToEpubIdentifier($stylesheet),
                "epubFileName" => basename($stylesheet),
            ];
        }
        foreach ($this->ebook->images as $image) {
            $identifiers[$this->idMarkers["image"] . $image->fileName] = [
                "epubIdentifier" => "lea-img-" . Girlfriend::comeToMe()->strToEpubIdentifier($image->fileName),
                "epubFileName" => Girlfriend::comeToMe()->strToEpubImageFileName($image->fileName),
            ];
        }
        $identifiers[$this->idMarkers["image"] . $this->ebook->cover] = [
            "epubIdentifier" => "lea-img-" . Girlfriend::comeToMe()->strToEpubIdentifier($this->ebook->cover),
            "epubFileName" => Girlfriend::comeToMe()->strToEpubImageFileName($this->ebook->cover),
        ];
        return $identifiers;
    }

    /**
     * Generates the cover xhtml from a template file, injecting the extracted cover file name.
     *
     * @return string
     */
    public function generateCoverFile(): string
    {
        return str_replace(
            search: "###",
            replace: Girlfriend::comeToMe()->strToEpubImageFileName($this->ebook->cover),
            subject: Girlfriend::comeToMe()->readFile(fileName: Girlfriend::$pathPurpleRain . "covertemplate.xhtml")
        );
    }

    /**
     * Generates the nav-xhtml.
     *
     * @return string
     */
    public function generateNavFile(): string
    {
        $format = "<li><a href='%s'>%s</a></li>%s";
        $toc = sprintf($format, $this->identifiers[$this->idMarkers["text"] . "cover.xhtml"]["epubFileName"], "Cover", PHP_EOL);
        foreach ($this->ebook->texts as $key => $text) {
            if (($text->title !== "Cover") && ($text->title !== "ePub Navigation"))
                $toc .= sprintf(
                    $format,
                    $this->identifiers[$this->idMarkers["text"] . $text->fileName]["epubFileName"],
                    $text->title, PHP_EOL
                );
        }
        $toc .= sprintf($format, $this->identifiers[$this->idMarkers["text"] . "nav.xhtml"]["epubFileName"], "Table of Contents", PHP_EOL);
        return str_replace(
            search: "###",
            replace: $toc,
            subject: Girlfriend::comeToMe()->readFile(fileName: Girlfriend::$pathPurpleRain . "navtemplate.xhtml")
        );
    }

    /**
     * Builds metadata block of content.opf.
     *
     * @return string
     */
    private function compileMetadata(): string
    {
        return Girlfriend::comeToMe()->readFile(fileName: REPO . "content.opf");
    }

    /**
     * Builds manifest from identifiers.
     *
     * @return string
     */
    private function compileManifest(): string
    {
        $manifest = "";
        foreach (Girlfriend::comeToMe()->arrayPregKeys(
            pattern: "/^" . $this->idMarkers["text"] . "/",
            array: $this->identifiers
        ) as $key => $text)
            $manifest .= sprintf(
                "<item id='%s' href='Text/%s' media-type='application/xhtml+xml'%s/>%s",
                $text["epubIdentifier"], $text["epubFileName"],
                $key === $this->idMarkers["text"] . "cover.xhtml" ? " properties='svg'"
                    : ($key === $this->idMarkers["text"] . "nav.xhtml" ? " properties='nav'" : ""), PHP_EOL
            );
        foreach (Girlfriend::comeToMe()->arrayPregKeys(
            pattern: "/^" . $this->idMarkers["font"] . "/",
            array: $this->identifiers
        ) as $font)
            $manifest .= sprintf("<item id='%s' href='Fonts/%s' media-type='font/%s'/>%s",
                $font["epubIdentifier"], $font["epubFileName"],
                pathinfo($font["epubFileName"])['extension'], PHP_EOL);
        foreach (Girlfriend::comeToMe()->arrayPregKeys(
            pattern: "/^" . $this->idMarkers["stylesheet"] . "/",
            array: $this->identifiers
        ) as $stylesheet)
            $manifest .= sprintf("<item id='%s' href='Styles/%s' media-type='text/css'/>%s",
                $stylesheet["epubIdentifier"], $stylesheet["epubFileName"], PHP_EOL);
        foreach (Girlfriend::comeToMe()->arrayPregKeys(
            pattern: "/^" . $this->idMarkers["image"] . "/",
            array: $this->identifiers
        ) as $image)
            $manifest .= sprintf("<item id='%s' href='Images/%s' media-type='image/%s'/>%s",
                $image["epubIdentifier"], $image["epubFileName"],
                pathinfo($image["epubFileName"])['extension'] === "jpg" ? "jpeg"
                    : pathinfo($image["epubFileName"])['extension'], PHP_EOL);
        return $manifest;
    }

    /**
     * Builds spine from identifiers.
     *
     * @return string
     */
    private function compileSpine(): string
    {
        $format = "<itemref idref='" . "%s" . "'/>%s";
        $identifiers = Girlfriend::comeToMe()->arrayPregKeys(
            pattern: "/^" . $this->idMarkers["text"] . "/",
            array: $this->identifiers
        );
        $spine = sprintf($format, $identifiers[$this->idMarkers["text"] . "cover.xhtml"]["epubIdentifier"], PHP_EOL);
        unset($identifiers[$this->idMarkers["text"] . "cover.xhtml"]);
        foreach ($identifiers as $key => $val)
            if ($key !== $this->idMarkers["text"] . "nav.xhtml")
                $spine .= sprintf($format, $val["epubIdentifier"], PHP_EOL);
        $spine .= sprintf($format, $identifiers[$this->idMarkers["text"] . "nav.xhtml"]["epubIdentifier"], PHP_EOL);
        return $spine;
    }

    /**
     * Builds guide block of content.opf.
     *
     * @return string
     */
    private function compileGuide(): string
    {
        return "<reference type=\"cover\" title=\"Cover\" href=\"Text/CoverByLeaEPubAnvilV0020.xhtml\"/>"
            . "<reference type=\"toc\" title=\"Table of Contents\" href=\"Text/AboutTheContentByEduardPech.xhtml\"/>"
            . "<reference type=\"acknowledgements\" title=\"Acknowledgements\" href=\"Text/AboutTheAuthorsByEduardPech.xhtml\"/>"
            . "<reference type=\"colophon\" title=\"Colophon\" href=\"Text/AboutThisPublicationByEduardPech.xhtml\"/>"
            . "<reference type=\"other.backmatter\" title=\"Back Matter\" href=\"Text/AboutThePublisherByEduardPech.xhtml\"/>"
            . "<reference type=\"text\" title=\"Text\" href=\"Text/AQuestionOfCourageByJesseFBone.xhtml\"/>";
    }

    /**
     * Compiles the OPF structure for the ePub content.opf file.
     *
     * @return string
     */
    private function compileOpf(): string
    {
        return "<?xml version=\"1.0\" encoding=\"utf-8\"?>"
            . "<package version=\"3.0\" unique-identifier=\"isbn\" xmlns=\"http://www.idpf.org/2007/opf\">"
            . "<metadata xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:opf=\"http://www.idpf.org/2007/opf\">"
            . $this->compileMetadata() . "</metadata>"
            . "<manifest>" . $this->compileManifest() . "</manifest>"
            . "<spine>" . $this->compileSpine() . "</spine>"
            . "<guide>" . $this->compileGuide() . "</guide>"
            . "</package>" . PHP_EOL;
    }

    /**
     * Now I can touch
     * Now I can feel
     *
     * @return bool
     * @throws DOMException
     */
    #[NoDiscard]
    public function conductor(): bool
    {
        $this->theOverture(); // ascertain we're laughing in the purple rain
        $zip = new ZipArchive();
        $zip->open(
            filename: Girlfriend::$pathEpubs . $this->ebook->title . " - " . $this->ebook->publisher->imprint . ".epub",
            flags: ZipArchive::CREATE | ZipArchive::OVERWRITE
        );
        $zip->addFile(filepath: Girlfriend::$pathPurpleRain . "mimetype", entryname: "mimetype");
        $zip->setCompressionName(name: 'mimetype', method: ZipArchive::CM_STORE);
        $zip->addFile(filepath: Girlfriend::$pathPurpleRain . "container.xml", entryname: "META-INF/container.xml");
        $zip->addFromString(name: "OEBPS/content.opf", content: $this->compileOpf());
        foreach ($this->ebook->fonts as $font)
            $zip->addFile(
                filepath: Girlfriend::$pathFonts . $font,
                entryname: "OEBPS/Fonts/" . $this->identifiers[$this->idMarkers["font"] . $font]["epubFileName"],
            );
        foreach ($this->ebook->stylesheets as $stylesheet)
            $zip->addFile(
                filepath: Girlfriend::$pathStyles . $stylesheet,
                entryname: "OEBPS/Styles/" . $this->identifiers[$this->idMarkers["stylesheet"] . $stylesheet]["epubFileName"],
            );
        foreach ($this->ebook->images as $image)
            $zip->addFile(
                filepath: Girlfriend::$pathImages . Girlfriend::$memory["subfolder"] . $image->fileName,
                entryname: "OEBPS/Images/" . $this->identifiers[$this->idMarkers["image"] . $image->fileName]["epubFileName"]
            );
        $zip->addFile(filepath: Girlfriend::$pathImages . Girlfriend::$memory["subfolder"] . $this->ebook->cover,
            entryname: "OEBPS/Images/" . $this->identifiers[$this->idMarkers["image"] . $this->ebook->cover]["epubFileName"]);
        foreach ($this->ebook->texts as $text) {
            $zip->addFromString(
                name: "OEBPS/Text/" . $this->identifiers[$this->idMarkers["text"] . $text->fileName]["epubFileName"],
                content: XMLetsGoCrazy::stripLeaDom(XMLetsGoCrazy::reWrapDom($text->dom, $text->title))->saveXML() ?: ""
            );
        }
        $zip->close();
        return true;
    }
}
