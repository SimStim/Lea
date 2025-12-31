<?php

declare(strict_types=1);

namespace Lea\Domain;

use Exception;
use Lea\Girlfriend;

/**
 * Text domain class
 */
final class Text
{
    /**
     * @throws Exception
     */
    public function __construct(
        string                    $fileName,
        private(set) string       $title = "" {
            set => trim(string: $value);
        },
        private(set) Author|array $author = [],
        public string             $blurb = "" {
            set => trim(string: $value);
        },
        private(set) string       $xhtml = "" {
            set => trim(string: $value);
        }
    )
    {
        // TODO: replace hacky error handling below
        $fileNamePath = REPO . "/text/" . $fileName;
        $annaStesia = Girlfriend::comeToMe();
        $this->xhtml = $annaStesia->readFileOrDie($fileNamePath);
        $title = XMLetsGoCrazy::extractSimpleTag(xhtml: $this->xhtml, tagName: "title");
        $titleNo = sizeof($title);
        if ($titleNo !== 1) throw new Exception("Need exactly one title. $titleNo found.");
        $this->title = $title[0];
        $authors = XMLetsGoCrazy::extractSimpleTag(xhtml: $this->xhtml, tagName: "author");
        $authorNo = sizeof($authors);
        if ($authorNo < 1) throw new Exception("Need at least one author. None found.");
        $this->author = $authors;
        $blurb = XMLetsGoCrazy::extractSimpleTag(xhtml: $this->xhtml, tagName: "blurb");
        $blurbNo = sizeof($blurb);
        if ($blurbNo > 1) throw new Exception("Need a maximum of one blurb. $blurbNo blurbs found.");
        $this->blurb = $blurb[0] ?? "";
    }
}
