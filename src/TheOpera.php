<?php

declare(strict_types=1);

namespace Lea;

use NoDiscard;
use ZipArchive;
use Lea\Adore\Girlfriend;
use Lea\Domain\Ebook;

final class TheOpera
{
    private array $identifiers {
        get => $this->identifiers ??= $this->buildIdentifiers();
    }
    private array $idMarkers = [
        "text" => "lea:text:",
        "image" => "lea:image:",
        "author" => "lea:creator:",
        "contributor" => "lea:contributor:",
    ];

    public function __construct(private(set) readonly Ebook $ebook)
    {
    }

    /**
     * The Overture:
     * - happens before the opera
     * - sets the themes
     * - establishes consequences
     * - tells the audience how to listen
     *
     * @return void (also known as Caesura)
     */
    private static function theOverture(): void
    {
        $hashes = [
            'PurpleRain.txt' => '247e5c56d2619ee9d29c4c56d69cacf917b49a572696ea60ba742d365b983112',
            'mimetype' => 'e468e350d1143eb648f60c7b0bd6031101ec0544a361ca74ecef256ac901f48b',
            'container.xml' => 'c54cb884813a53ce2fc9b3102ca8ee5c03b0397a2cb984500830e86c65ec092f',
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
    private function buildIdentifiers(): array
    {
        $identifiers = [];
        foreach ($this->ebook->authors as $author) {
            $identifiers[$this->idMarkers["author"] . $author->name] = [
                "epubIdentifier" => "lea-cre-" . Girlfriend::comeToMe()->strToEpubIdentifier($author->name),
                "epubTextFileName" => "",
                "epubImageFileName" => "",
            ];
        }
        foreach ($this->ebook->contributors as $contributor) {
            $identifiers[$this->idMarkers["contributor"] . $contributor->name] = [
                "epubIdentifier" => "lea-con-" . Girlfriend::comeToMe()->strToEpubIdentifier($contributor->name),
                "epubTextFileName" => "",
                "epubImageFileName" => "",
            ];
        }
        foreach ($this->ebook->texts as $text) {
            $title = $text->title . " by " . $text->authors[0]->name;
            $identifiers[$this->idMarkers["text"] . $text->fileName] = [
                "epubIdentifier" => "lea-txt-" . Girlfriend::comeToMe()->strToEpubIdentifier($title),
                "epubTextFileName" => Girlfriend::comeToMe()->strToEpubTextFileName($title),
                "epubImageFileName" => "",
            ];
            foreach ($text->authors as $author) {
                $identifiers[$this->idMarkers["author"] . $author->name] = [
                    "epubIdentifier" => "lea-cre-" . Girlfriend::comeToMe()->strToEpubIdentifier($author->name),
                    "epubTextFileName" => "",
                    "epubImageFileName" => "",
                ];
            }
        }
        foreach ($this->ebook->images as $image) {
            $identifiers[$this->idMarkers["image"] . $image] = [
                "epubIdentifier" => "lea-img-" . Girlfriend::comeToMe()->strToEpubIdentifier($image),
                "epubTextFileName" => "",
                "epubImageFileName" => Girlfriend::comeToMe()->strToEpubImageFileName($image),
            ];
        }
        return $identifiers;
    }

    /**
     * Builds metadata block of content.opf.
     *
     * @return string
     */
    private function buildMetadata(): string
    {
        return Girlfriend::comeToMe()->readFile(fileName: REPO . "content.opf");
    }

    /**
     * Builds manifest from identifiers.
     *
     * @return string
     */
    private function buildManifest(): string
    {
        $manifest = "";
        foreach (Girlfriend::comeToMe()->arrayPregKeys(
            pattern: "/^" . $this->idMarkers["text"] . "/",
            array: $this->identifiers
        ) as $text)
            $manifest .= sprintf(
                "<item id='%s' href='Text/%s' media-type='application/xhtml+xml'/>%s",
                $text["epubIdentifier"], $text["epubTextFileName"], PHP_EOL
            );
        foreach (Girlfriend::comeToMe()->arrayPregKeys(
            pattern: "/^" . $this->idMarkers["image"] . "/",
            array: $this->identifiers
        ) as $image)
            $manifest .= sprintf("<item id='%s' href='Images/%s' media-type='image/jpeg'/>%s",
                $image["epubIdentifier"], $image["epubImageFileName"], PHP_EOL);
        return $manifest;
    }

    /**
     * Builds spine from identifiers.
     *
     * @return string
     */
    private function buildSpine(): string
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
    private function buildGuide(): string
    {
        return "<reference type=\"cover\" title=\"Cover\" href=\"Text/cover.xhtml\"/>"
            . "<reference type=\"toc\" title=\"Table of Contents\" href=\"Text/AboutTheContent.xhtml\"/>"
            . "<reference type=\"acknowledgements\" title=\"Acknowledgements\" href=\"Text/AboutTheAuthors.xhtml\"/>"
            . "<reference type=\"colophon\" title=\"Colophon\" href=\"Text/AboutThisPublication.xhtml\"/>"
            . "<reference type=\"other.backmatter\" title=\"Back Matter\" href=\"Text/AboutThePublisher.xhtml\"/>"
            . "<reference type=\"text\" title=\"Text\" href=\"Text/AQuestionOfCourageByJesseFBone.xhtml\"/>";
    }

    /**
     * Now I can touch
     * Now I can feel
     *
     * @return bool
     */
    #[NoDiscard]
    public function conductor(): bool
    {
        self::theOverture(); // ascertain we're laughing in the purple rain
        $zip = new ZipArchive();
        $zip->open(
            filename: Girlfriend::$pathEpubs . $this->ebook->title . " - " . $this->ebook->publisher->imprint . ".epub",
            flags: ZipArchive::CREATE | ZipArchive::OVERWRITE
        );
        $zip->addFile(filepath: Girlfriend::$pathPurpleRain . "mimetype", entryname: "mimetype");
        $zip->setCompressionName(name: 'mimetype', method: ZipArchive::CM_STORE);
        $zip->addFile(filepath: Girlfriend::$pathPurpleRain . "container.xml", entryname: "META-INF/container.xml");
        $opf = "<?xml version=\"1.0\" encoding=\"utf-8\"?>"
            . "<package version=\"3.0\" unique-identifier=\"isbn\" xmlns=\"http://www.idpf.org/2007/opf\">"
            . "<metadata xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:opf=\"http://www.idpf.org/2007/opf\">"
            . $this->buildMetadata() . "</metadata>"
            . "<manifest>" . $this->buildManifest() . "</manifest>"
            . "<spine>" . $this->buildSpine() . "</spine>"
            . "<guide>" . $this->buildGuide() . "</guide>"
            . "</package>" . PHP_EOL;
        $zip->addFromString("OEBPS/content.opf", $opf);
        foreach ($this->ebook->texts as $text)
            $zip->addFile(
                filepath: Girlfriend::$pathText . $text->fileName,
                entryname: "OEBPS/Text/" . $this->identifiers[$this->idMarkers["text"] . $text->fileName]["epubTextFileName"]
            );
        foreach ($this->ebook->images as $image)
            $zip->addFile(
                filepath: Girlfriend::$pathImages . $image,
                entryname: "OEBPS/Images/" . $this->identifiers[$this->idMarkers["image"] . $image]["epubImageFileName"]
            );
        $zip->close();
        return true;
    }
}
