#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Lea Controversy Test Suite
 *
 * Proves that every Controversy is voiced correctly: the genuine pipeline (lea.php)
 * is run against a mutated copy of the golden fixture (tests/arx/), inside a
 * throw-away sandbox directory, and the cry's label, message, and the process exit
 * code are asserted.
 *
 * Lea resolves her content root from the current working directory (REPO), so every
 * case spawns lea.php with the sandbox as its CWD. The real arx/ tree is never
 * touched, and no EPUB ever lands outside the sandbox.
 *
 * Usage:
 *   php tests/controversies.php [--list] [--filter=Name] [--keep] [--ci | --no-ci]
 *
 *   --list           List every controversy and what is expected, then exit.
 *   --filter=Name    Only run (or list) controversies whose name contains "Name".
 *   --keep           Keep sandbox directories for post-mortem inspection.
 *   --ci / --no-ci   Force GitHub Actions workflow commands ("::group::") on/off.
 *                    By default they are emitted when STDOUT is not a terminal.
 *   -h, --help       Show the help page and exit.
 *
 * Exit code: the number of failed controversies, capped at 255.
 */

error_reporting(error_level: E_ALL);
ini_set(option: "display_errors", value: "1");

const LEA = __DIR__ . "/../lea.php";
const ARX = __DIR__ . "/arx";

const LABEL_INFO = "[ INFO ]";
const LABEL_WARNING = "[ WARNING ]";
const LABEL_SEVERE = "[ SEVERE ]";
const LABEL_FATAL = "[ FATAL ]";
const LABEL_DEBUG = "[ DEBUG ]";

/**
 * One controversy, one expectation.
 *
 * Either a process case (mutate a sandbox fixture, spawn lea.php, assert label,
 * message, and exit code) or an in-process unit case ($unit). $skip reports SKIP
 * without running, with the reason spelled out.
 */
final class ControversyCase
{
    public function __construct(
        public string $group,
        public string $name,
        public int $exit = 0,
        public string $label = "",
        public string $frag = "",
        public ?Closure $mutate = null,
        public ?string $skip = null,
        public ?string $needle = null,
        public ?Closure $unit = null,
    ) {
    }

    /** The exact string the normalized output must contain. */
    public function needle(): string
    {
        return $this->needle ?? $this->label . "\n" . $this->frag;
    }
}

/**
 * The full case table, in Controversy declaration order.
 *
 * Message fragments are pinned to the first line of the cry: cries are echoed as
 * "[ LABEL ]\n<message>", while the closing legend also contains every label —
 * so "label followed by message" is the only unambiguous match.
 *
 * @return array<string, list<ControversyCase>>
 */
function buildCases(): array
{
    return [

        "ebook (ebook config: arx/ebooks/golden.xml)" => [
            new ControversyCase(
                group: "ebook",
                name: "EbookNotWellFormed",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The content of the ebook config file is not well formed.",
                mutate: fn(string $s) => editEbook($s, fn($x) => $x . "<broken"),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookReadError",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The ebook XML config file could not be read.",
                mutate: fn(string $s) => editEbook($s, fn($x) => ""),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookTitleRequired",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The title is required.",
                mutate: fn(string $s) => editEbook($s, fn($x) => dropLine($x, "<lea:title>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookMultipleTitles",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Multiple title tags defined in ebook.",
                mutate: fn(string $s) => editEbook($s, fn($x) => duplicateLine($x, "<lea:title>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookDescriptionRecommended",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "The description is not mandatory, but highly recommended.",
                mutate: fn(string $s) => editEbook($s, fn($x) => dropLine($x, "<lea:description>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookMultipleDescriptions",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "Multiple description tags defined in ebook.",
                mutate: fn(string $s) => editEbook($s, fn($x) => duplicateLine($x, "<lea:description>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookPublisherMandatory",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The publisher data is mandatory.",
                // no ">" in the fragment: the golden tag carries the mandatory contact attribute
                mutate: fn(string $s) => editEbook($s, fn($x) => dropLine($x, "<lea:publisher")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookRightsRecommended",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "The rights declaration is not mandatory, but highly recommended.",
                mutate: fn(string $s) => editEbook($s, fn($x) => dropLine($x, "<lea:rights>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookMultipleRights",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "Multiple rights tags defined in ebook.",
                mutate: fn(string $s) => editEbook($s, fn($x) => duplicateLine($x, "<lea:rights>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookLanguageRecommended",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "The language declaration is not mandatory, but highly recommended.",
                mutate: fn(string $s) => editEbook($s, fn($x) => dropLine($x, "<lea:language>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookAuthorRequired",
                exit: 1,
                label: LABEL_FATAL,
                frag: "At least one author is required.",
                mutate: fn(string $s) => editEbook($s, fn($x) => dropLine($x, "<lea:author>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookInvalidAuthor",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Invalid author tag(s) detected in ebook.",
                mutate: fn(string $s) => editEbook($s, fn($x) => dropLine($x, "<lea:author>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookInvalidDate",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The date is missing or invalid; possibly multiple date tags declared.",
                mutate: fn(string $s) => editEbook($s, fn($x) => str_replace(
                    search: ["January 1, 2026", "January 2, 2026"],
                    replace: "once upon a time",
                    subject: $x,
                )),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookInvalidContributor",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Invalid contributor tag(s) detected in ebook.",
                mutate: fn(string $s) => editEbook($s, fn($x) => str_replace(
                    search: 'roles="bkp"',
                    replace: 'roles="bogus"',
                    subject: $x,
                )),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookInvalidISBN",
                exit: 0,
                label: LABEL_SEVERE,
                // extractISBN() substitutes "***INVALID***" when no tag carries a valid ISBN
                frag: "The ISBN '***INVALID***' is invalid.",
                mutate: fn(string $s) => editEbook($s, fn($x) => str_replace(
                    search: "978-3-16-148410-0",
                    replace: "978-3-16-148410-1",
                    subject: $x,
                )),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookMultipleISBNs",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "Multiple ISBN tags defined in ebook.",
                mutate: fn(string $s) => editEbook($s, fn($x) => duplicateLine($x, "<lea:isbn>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookSubjectRecommended",
                exit: 0,
                label: LABEL_WARNING,
                frag: "No subject declarations found in ebook. Declarations are highly recommended.",
                mutate: fn(string $s) => editEbook($s, fn($x) => dropLine($x, "<lea:subject>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookInvalidCover",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The cover file name was defined, but the file cannot be found in the file system.",
                mutate: fn(string $s) => editEbook($s, fn($x) => str_replace(
                    search: "<lea:cover>cover.jpg</lea:cover>",
                    replace: "<lea:cover>missing-cover.jpg</lea:cover>",
                    subject: $x,
                )),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookMultipleCovers",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Multiple cover tags defined in ebook.",
                mutate: fn(string $s) => editEbook($s, fn($x) => duplicateLine($x, "<lea:cover>")),
            ),
            new ControversyCase(
                group: "ebook",
                name: "EbookSubfolderTagUndefined",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "Requested tag 'bogus' is not defined for subfolders.",
                // an ADDITIONAL bogus subfolder: twisting tag="images" itself would break the
                // cover lookup and co-fire EbookInvalidCover instead
                mutate: fn(string $s) => editEbook($s, fn($x) => addLine(
                    subject: $x,
                    line: '<lea:subfolder tag="bogus">bogus</lea:subfolder>',
                    after: '<lea:subfolder tag="images">',
                )),
            ),
        ],

        "text (text content: arx/text/golden/golden.xhtml)" => [
            new ControversyCase(
                group: "text",
                name: "TextReadError",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The text content file could not be read.",
                mutate: fn(string $s) => editText($s, fn($x) => ""),
            ),
            new ControversyCase(
                group: "text",
                name: "TextNotWellFormed",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The content of the text config file is not well formed.",
                mutate: fn(string $s) => editText($s, fn($x) => $x . "<broken"),
            ),
            new ControversyCase(
                group: "text",
                name: "TextTitleRequired",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The title is required.",
                mutate: fn(string $s) => editText($s, fn($x) => dropLine($x, "<lea:title>")),
            ),
            new ControversyCase(
                group: "text",
                name: "TextMultipleTitles",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "Multiple title tags defined in text.",
                mutate: fn(string $s) => editText($s, fn($x) => duplicateLine($x, "<lea:title>")),
            ),
            new ControversyCase(
                group: "text",
                name: "TextAuthorRequired",
                exit: 1,
                label: LABEL_FATAL,
                frag: "At least one author is required.",
                mutate: fn(string $s) => editText($s, fn($x) => dropLine($x, "<lea:author>")),
            ),
            new ControversyCase(
                group: "text",
                name: "TextInvalidAuthor",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Invalid author tag(s) detected in text.",
                mutate: fn(string $s) => editText($s, fn($x) => dropLine($x, "<lea:author>")),
            ),
            new ControversyCase(
                group: "text",
                name: "TextMultipleBlurbs",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "Multiple blurbs found in text.",
                mutate: fn(string $s) => editText($s, fn($x) => addLine(
                    subject: addLine(subject: $x, line: "<lea:blurb>Blurb one.</lea:blurb>", after: "<lea:author>"),
                    line: "<lea:blurb>Blurb two.</lea:blurb>",
                    after: "<lea:author>",
                )),
            ),
            new ControversyCase(
                group: "text",
                name: "TextScriptUndefined",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "Requested script is not defined.",
                mutate: fn(string $s) => editText($s, fn($x) => addLine(
                    subject: $x,
                    line: "<lea:script>bogus script</lea:script>",
                    after: "<lea:author>",
                )),
            ),
        ],

        "image (image tags and image scripts, added to the golden text)" => [
            new ControversyCase(
                group: "image",
                name: "ImageSourceUndefined",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Image source was not defined.",
                mutate: fn(string $s) => editText($s, fn($x) => addLine(
                    subject: $x,
                    line: "<lea:image>cover.jpg</lea:image>",
                    after: "<p>",
                )),
            ),
            new ControversyCase(
                group: "image",
                name: "ImageMultipleCaptions",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "Multiple captions defined in a DOM object",
                mutate: fn(string $s) => editText($s, fn($x) => addLine(
                    subject: $x,
                    line: '<lea:image source="cover.jpg">'
                        . "<lea:caption>One</lea:caption><lea:caption>Two</lea:caption></lea:image>",
                    after: "<p>",
                )),
            ),
            new ControversyCase(
                group: "image",
                name: "ImageLinkedMissingTo",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Script 'linkedImage' is missing the missing the mandatory 'to' attribute.",
                mutate: fn(string $s) => editText($s, fn($x) => addLine(
                    subject: $x,
                    line: '<lea:script image="cover.jpg">linked image</lea:script>',
                    after: "<p>",
                )),
            ),
            new ControversyCase(
                group: "image",
                name: "ImageLinkedMissingImage",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Script 'linkedImage' is missing the missing the mandatory 'image' attribute.",
                mutate: fn(string $s) => editText($s, fn($x) => addLine(
                    subject: $x,
                    line: '<lea:script to="Golden Chapter">linked image</lea:script>',
                    after: "<p>",
                )),
            ),
            new ControversyCase(
                group: "image",
                name: "ImageLockedMissingImage",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Script 'lockedImage' is missing the missing the mandatory 'image' attribute.",
                mutate: fn(string $s) => editText($s, fn($x) => addLine(
                    subject: $x,
                    line: "<lea:script>locked image</lea:script>",
                    after: "<p>",
                )),
            ),
            new ControversyCase(
                group: "image",
                name: "ImageReadError",
                skip: "its call site is commented out in PaisleyPark, so it cannot be triggered end-to-end",
            ),
        ],

        "link (link targets and external URL checks)" => [
            new ControversyCase(
                group: "link",
                name: "LinkTargetUndefined",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Link to undefined link target.",
                mutate: fn(string $s) => editText($s, fn($x) => addLine(
                    subject: $x,
                    line: '<lea:link to="Nowhere At All">gone</lea:link>',
                    after: "<p>",
                )),
            ),
            new ControversyCase(
                group: "link",
                name: "LinkUrlsNotChecked",
                exit: 0,
                label: LABEL_INFO,
                frag: "External links were not checked this time.",
            ),
            new ControversyCase(
                group: "link",
                name: "LinkExternalCheckFailed",
                skip: "requires the external network and a URL that answers with HTTP >= 400",
            ),
            new ControversyCase(
                group: "link",
                name: "LinkExternalCheckTimeout",
                skip: "requires the external network and a host that hangs instead of refusing",
            ),
        ],

        "epub (EPUB generation and EPUBCheck)" => [
            new ControversyCase(
                group: "epub",
                name: "EpubNotChecked",
                exit: 0,
                label: LABEL_INFO,
                frag: "EPUBCheck was not requested this time.",
            ),
            new ControversyCase(
                group: "epub",
                name: "EpubCheckFailure",
                skip: "requires the external app: java plus EPUBCheck",
            ),
        ],

        "core (low-level machinery)" => [
            // A missing file makes readFile() cry and throw; Girlfriend::extraordinary()
            // prints the oopsie and calls exit with no argument (exit code 0). The dove
            // is never voiced, so the oopsie text is the observable.
            new ControversyCase(
                group: "core",
                name: "CoreFileReadError",
                exit: 0,
                needle: "Error trying to read file",
                mutate: fn(string $s) => editEbook($s, fn($x) => addLine(
                    subject: $x,
                    line: "<lea:stylesheet>missing.css</lea:stylesheet>",
                    after: "<lea:stylesheet>golden.css",
                )),
            ),
            new ControversyCase(
                group: "core",
                name: "CoreFileReadSimilar",
                exit: 0,
                label: LABEL_SEVERE,
                frag: "File not found, reading a similar file.",
                mutate: fn(string $s) => editEbook($s, fn($x) => str_replace(
                    search: "<lea:stylesheet>golden.css</lea:stylesheet>",
                    replace: "<lea:stylesheet>Golden.css</lea:stylesheet>",
                    subject: $x,
                )),
            ),
            // The block file must exist but be empty: a missing file would take the
            // CoreFileReadError path instead, and only an empty read yields CoreBlockReadError.
            new ControversyCase(
                group: "core",
                name: "CoreBlockReadError",
                exit: 1,
                label: LABEL_FATAL,
                frag: "The block content file could not be read.",
                mutate: function (string $s): void {
                    editText($s, fn($x) => addLine(
                        subject: $x,
                        line: "<lea:block>empty-block.xhtml</lea:block>",
                        after: "<p>",
                    ));
                    emptyFile(sandbox: $s, relativePath: "blocks/empty-block.xhtml");
                },
            ),
            new ControversyCase(
                group: "core",
                name: "CorePreflightErrors",
                exit: 1,
                label: LABEL_FATAL,
                frag: "Preflight found errors with lea:tag names.",
                mutate: fn(string $s) => editText($s, fn($x) => addLine(
                    subject: $x,
                    line: "<lea:autor></lea:autor>",
                    after: "<lea:author>",
                )),
            ),
            new ControversyCase(
                group: "core",
                name: "CoreGirlfriendMemory",
                label: LABEL_DEBUG,
                unit: function (): ?string {
                    // Girlfriend's static path properties are constant expressions over the
                    // globals lea.php defines; the unit runs without lea.php, so define them.
                    if (!defined("ROOT"))
                        define(constant_name: "ROOT", value: dirname(path: __DIR__));
                    if (!defined("REPO"))
                        define(constant_name: "REPO", value: ARX . "/");
                    require_once dirname(path: __DIR__) . "/vendor/autoload.php";

                    ob_start();
                    $girlfriend = Lea\Adore\Girlfriend::comeToMe();
                    $before = count($girlfriend->doveCries);
                    $value = $girlfriend->recall(name: "controversies-unit-test-key");
                    ob_end_clean();

                    if ($value !== "")
                        return "recall() should return an empty string, got '$value'.";
                    $cries = array_slice($girlfriend->doveCries, $before);
                    if (count($cries) !== 1)
                        return "expected exactly one dovecry, got " . count($cries) . ".";
                    if ($cries[0]->flaw !== Lea\Adore\Flaw::Debug)
                        return "expected Flaw::Debug, got " . $cries[0]->flaw->name . ".";
                    if (!str_contains($cries[0]->message, "controversies-unit-test-key"))
                        return "the cry does not name the forgotten key: '{$cries[0]->message}'.";
                    return null;
                },
            ),
        ],
    ];
}

// ---------------------------------------------------------------------------
// Fixture mutation helpers — every case receives a pristine copy of tests/arx.
// ---------------------------------------------------------------------------

/** Apply an edit to the ebook config file inside the sandbox. */
function editEbook(string $sandbox, Closure $edit): void
{
    $path = "$sandbox/arx/ebooks/golden.xml";
    file_put_contents(filename: $path, data: $edit((string)file_get_contents($path)));
}

/** Apply an edit to the golden text file inside the sandbox. */
function editText(string $sandbox, Closure $edit): void
{
    $path = "$sandbox/arx/text/golden/golden.xhtml";
    file_put_contents(filename: $path, data: $edit((string)file_get_contents($path)));
}

/** Create an empty file inside the sandbox's arx tree. */
function emptyFile(string $sandbox, string $relativePath): void
{
    file_put_contents(filename: "$sandbox/arx/$relativePath", data: "");
}

/** Remove the first line containing $fragment. */
function dropLine(string $subject, string $fragment): string
{
    return (string)preg_replace_callback(
        pattern: "/^.*" . preg_quote($fragment, "/") . ".*\n?/m",
        callback: fn() => "",
        subject: $subject,
        limit: 1,
    );
}

/** Duplicate the first line containing $fragment. */
function duplicateLine(string $subject, string $fragment): string
{
    return (string)preg_replace_callback(
        pattern: "/^.*" . preg_quote($fragment, "/") . ".*$/m",
        callback: fn($m) => $m[0] . "\n" . $m[0],
        subject: $subject,
        limit: 1,
    );
}

/** Insert $line after the first line containing $after. */
function addLine(string $subject, string $line, string $after): string
{
    return (string)preg_replace_callback(
        pattern: "/^.*" . preg_quote($after, "/") . ".*$/m",
        callback: fn($m) => $m[0] . "\n" . $line,
        subject: $subject,
        limit: 1,
    );
}

// ---------------------------------------------------------------------------
// Sandbox and process plumbing.
// ---------------------------------------------------------------------------

function recursiveCopy(string $source, string $target): void
{
    if (is_dir($source)) {
        mkdir(directory: $target, permissions: 0777, recursive: true);
        foreach (scandir($source) as $entry) {
            if (in_array($entry, [".", "..", ".gitignore"], strict: true))
                continue;
            recursiveCopy(source: "$source/$entry", target: "$target/$entry");
        }
    } else {
        copy($source, $target);
    }
}

function recursiveDelete(string $path): void
{
    if (!is_dir($path)) {
        @unlink($path);
        return;
    }
    foreach (scandir($path) as $entry)
        if (!in_array($entry, [".", ".."], strict: true))
            recursiveDelete(path: "$path/$entry");
    @rmdir($path);
}

function makeSandbox(string $caseName): string
{
    $sandbox = sys_get_temp_dir() . "/lea-controversy-$caseName-" . uniqid();
    recursiveCopy(source: ARX, target: "$sandbox/arx");
    return $sandbox;
}

/**
 * Run lea.php on the golden fixture with the sandbox as content root.
 * @return array{int, string} [exit code, combined stdout+stderr]
 */
function runLea(string $sandbox): array
{
    $command = escapeshellarg(PHP_BINARY) . " " . escapeshellarg(LEA) . " golden.xml 2>&1";
    $process = proc_open($command, [1 => ["pipe", "w"]], $pipes, cwd: $sandbox);
    if (!is_resource($process))
        return [-1, "proc_open failed"];
    $output = (string)stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $code = proc_close($process);
    return [$code, $output];
}

/** Strip ANSI escape sequences so needles can match plainly. */
function normalize(string $output): string
{
    return (string)preg_replace(pattern: '/\e\[[?0-9;]*[A-Za-z]/', replacement: "", subject: $output);
}

/** Print the help page and stop. With $error, complain to STDERR instead. */
function exitWithUsage(string $error = "", int $code = 0): never
{
    $help = <<<HELP
        Lea Controversy Test Suite

        Every Controversy she can voice, proven end-to-end: each case runs the
        genuine pipeline (lea.php) against a mutated copy of the golden fixture
        (tests/arx/) inside a throw-away sandbox, then asserts the cry's label,
        its message, and the exit code.

        Usage:
          php tests/controversies.php [options]

        Options:
          --list           List every controversy and its expectation, then exit.
          --filter=Name    Only run (or list) controversies whose name contains "Name".
          --keep           Keep sandbox directories for post-mortem inspection.
          --ci             Emit GitHub Actions workflow commands ("::group::").
          --no-ci          Never emit them. Default: emit when STDOUT is not a terminal.
          -h, --help       Show this help and exit.

        Exit code:
          The number of failed controversies, capped at 255.

        Skipped by design:
          EpubCheckFailure           needs java plus EPUBCheck
          LinkExternalCheckFailed    needs the external network and a failing URL
          LinkExternalCheckTimeout   needs the external network and a hanging host
          ImageReadError             its call site is commented out in PaisleyPark

        HELP;
    if ($error === "")
        echo $help;
    else
        fwrite(stream: STDERR, data: $error . PHP_EOL . PHP_EOL . $help);
    exit($code);
}

// ---------------------------------------------------------------------------
// The suite itself.
// ---------------------------------------------------------------------------

$useColor = stream_isatty(stream: STDOUT);
define(constant_name: "C_PASS", value: $useColor ? "\e[32m" : "");
define(constant_name: "C_FAIL", value: $useColor ? "\e[1;31m" : "");
define(constant_name: "C_SKIP", value: $useColor ? "\e[33m" : "");
define(constant_name: "C_DIM", value: $useColor ? "\e[2m" : "");
define(constant_name: "C_BOLD", value: $useColor ? "\e[1m" : "");
define(constant_name: "C_OFF", value: $useColor ? "\e[0m" : "");

$list = false;
$keep = false;
$filter = null;
$ci = null;

foreach (array_slice($argv, 1) as $argument) {
    match (true) {
        $argument === "--list" => $list = true,
        $argument === "--keep" => $keep = true,
        $argument === "--ci" => $ci = true,
        $argument === "--no-ci" => $ci = false,
        str_starts_with($argument, "--filter=") => $filter = substr($argument, 9),
        $argument === "-h" || $argument === "--help" => exitWithUsage(),
        default => exitWithUsage(error: "Unknown option: $argument", code: 2),
    };
}

$ci ??= !stream_isatty(stream: STDOUT);

if (!file_exists(LEA) || !is_dir(ARX))
    exit("Run this suite from the Lea repository: expected " . LEA . " and " . ARX . PHP_EOL);

$cases = buildCases();
if ($filter !== null) {
    $needle = strtolower($filter);
    $cases = array_map(
        callback: fn(array $group) => array_values(array_filter(
            array: $group,
            callback: fn(ControversyCase $c) => str_contains(strtolower($c->name), $needle),
        )),
        array: $cases,
    );
    $cases = array_filter($cases, callback: fn(array $group) => $group !== []);
}

$total = array_sum(array: array_map(callback: count(...), array: $cases));

if ($list) {
    foreach ($cases as $group => $groupCases) {
        echo C_BOLD . $group . C_OFF . PHP_EOL;
        foreach ($groupCases as $case) {
            $expectation = $case->skip !== null
                ? "SKIPPED — " . $case->skip
                : ($case->unit !== null
                    ? "{$case->label} (in-process unit)"
                    : "{$case->label} exit {$case->exit}");
            printf("  %-26s %s" . PHP_EOL, $case->name, $expectation);
        }
    }
    exit(0);
}

echo "Lea Controversy Test Suite — $total cases." . PHP_EOL;

$passed = $failed = $skipped = 0;

foreach ($cases as $group => $groupCases) {
    if ($ci)
        echo "::group::$group" . PHP_EOL;
    else
        echo PHP_EOL . C_BOLD . "== $group ==" . C_OFF . PHP_EOL;

    foreach ($groupCases as $case) {
        if ($case->skip !== null) {
            $skipped++;
            echo "  " . C_SKIP . "SKIP" . C_OFF . "  {$case->name} " . C_DIM . "— {$case->skip}" . C_OFF . PHP_EOL;
            continue;
        }

        if ($case->unit !== null) {
            $error = ($case->unit)();
            if ($error === null) {
                $passed++;
                echo "  " . C_PASS . "PASS" . C_OFF . "  {$case->name}" . PHP_EOL;
            } else {
                $failed++;
                echo "  " . C_FAIL . "FAIL" . C_OFF . "  {$case->name}" . PHP_EOL
                    . "        - $error" . PHP_EOL;
            }
            continue;
        }

        $sandbox = makeSandbox(caseName: $case->name);
        try {
            if ($case->mutate !== null)
                ($case->mutate)($sandbox);
            [$code, $output] = runLea(sandbox: $sandbox);
            $normalized = normalize($output);
            $needle = $case->needle();

            $reasons = [];
            if ($code !== $case->exit)
                $reasons[] = "expected exit code {$case->exit}, got $code";
            if (!str_contains($normalized, $needle))
                $reasons[] = 'output missing: "' . str_replace("\n", "\\n", $needle) . '"';

            if ($reasons === []) {
                $passed++;
                $kept = $keep ? "  " . C_DIM . "(kept: $sandbox)" . C_OFF : "";
                echo "  " . C_PASS . "PASS" . C_OFF . "  {$case->name}$kept" . PHP_EOL;
            } else {
                $failed++;
                echo "  " . C_FAIL . "FAIL" . C_OFF . "  {$case->name}" . PHP_EOL;
                foreach ($reasons as $reason)
                    echo "        - $reason" . PHP_EOL;
                $tail = array_slice(explode(PHP_EOL, trim($normalized)), -20);
                echo C_DIM . "        ---- output tail ----" . PHP_EOL;
                foreach ($tail as $line)
                    echo "        " . $line . PHP_EOL;
                echo C_OFF;
                if ($keep)
                    echo "        sandbox kept at: $sandbox" . PHP_EOL;
            }
        } finally {
            if (!$keep)
                recursiveDelete(path: $sandbox);
        }
    }

    if ($ci)
        echo "::endgroup::" . PHP_EOL;
}

echo PHP_EOL
    . "$total controversies: $passed passed, $failed failed, $skipped skipped." . PHP_EOL
    . ($failed > 0 && !$keep ? "Re-run with --keep to inspect failing sandboxes." . PHP_EOL : "");

exit(min(255, $failed));
