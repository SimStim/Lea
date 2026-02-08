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
            "navtemplate.xhtml" => "18e513639cc10f7d77783991270b29d7a1965ef59eff09d262a18fce627d78d8",
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
        if ($this->ebook->cover !== "")
            $identifiers[$this->idMarkers["text"] . $this->ebook->cover] = [
                "epubIdentifier" => "lea-txt-cover-xhtml",
                "epubFileName" => "cover.xhtml",
            ];
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
            $identifiers[$this->idMarkers["image"] . $image] = [
                "epubIdentifier" => "lea-img-" . Girlfriend::comeToMe()->strToEpubIdentifier($image),
                "epubFileName" => Girlfriend::comeToMe()->strToEpubImageFileName($image),
            ];
        }
        return $identifiers;
    }

    /**
     * Generates the cover xhtml from a template file, injecting the extracted cover file name.
     *
     * @return string
     */
    private function generateCoverFile(): string
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
    private function generateNavFile(): string
    {
        $navXhtml = Girlfriend::comeToMe()->readFile(fileName: Girlfriend::$pathPurpleRain . "navtemplate.xhtml");
        $toc = ($this->ebook->cover === "" ?: "<li><a href='cover.xhtml'>Cover</a></li>" . PHP_EOL);
        foreach ($this->ebook->texts as $text)
            $toc .= sprintf(
                "<li><a href='%s'>%s</a></li>%s",
                $this->identifiers[$this->idMarkers["text"] . $text->fileName]["epubFileName"],
                $text->title, PHP_EOL
            );
        return str_replace(
            search: "###",
            replace: $toc,
            subject: $navXhtml
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
        ) as $text)
            $manifest .= sprintf(
                "<item id='%s' href='Text/%s' media-type='application/xhtml+xml'%s/>%s",
                $text["epubIdentifier"], $text["epubFileName"],
                $text["epubFileName"] === "cover.xhtml"
                    ? " properties='svg'"
                    : ($text["epubFileName"] === "nav.xhtml"
                    ? " properties='nav'"
                    : ""), PHP_EOL
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
                pathinfo($image["epubFileName"])['extension'] === "jpg"
                    ? "jpeg"
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
        $spine = "";
        foreach (Girlfriend::comeToMe()->arrayPregKeys(
            pattern: "/^" . $this->idMarkers["text"] . "/",
            array: $this->identifiers
        ) as $text)
            $spine .= sprintf("<itemref idref='" . "%s" . "'/>%s",
                $text["epubIdentifier"], PHP_EOL);
        return $spine;
    }

    /**
     * Builds guide block of content.opf.
     *
     * @return string
     */
    private function compileGuide(): string
    {
        return "<reference type=\"cover\" title=\"Cover\" href=\"Text/cover.xhtml\"/>"
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
        if ($this->ebook->cover !== "")
            $zip->addFromString(name: "OEBPS/Text/cover.xhtml", content: $this->generateCoverFile());
        foreach ($this->ebook->texts as $text)
            $zip->addFromString(
                name: "OEBPS/Text/" . $this->identifiers[$this->idMarkers["text"] . $text->fileName]["epubFileName"],
                content: XMLetsGoCrazy::stripLeaDom(XMLetsGoCrazy::reWrapDom($text->dom))->saveXML() ?: ""
            );
        $zip->addFromString(name: "OEBPS/Text/nav.xhtml", content: $this->generateNavFile());
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
                filepath: Girlfriend::$pathImages . $image,
                entryname: "OEBPS/Images/" . $this->identifiers[$this->idMarkers["image"] . $image]["epubFileName"]
            );
        $zip->close();
        return true;
    }
}
