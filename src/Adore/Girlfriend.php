<?php

declare(strict_types=1);

namespace Lea\Adore;

use BadMethodCallException;
use Exception;
use Lea\Domain\Ebook;
use Lea\Domain\Text;
use NoDiscard;
use SebastianBergmann\Version;
use Throwable;

/**
 * You can do it because I'm your friend.
 */
final class Girlfriend
{
    private static ?self $instance = null;
    private static string $minVersion = "⊙1.0.0";
    private(set) static string $pathEbooks = REPO . "ebooks/";
    private(set) static string $pathBlocks = REPO . "blocks/";
    private(set) static string $pathScripts = REPO . "scripts/";
    private(set) static string $pathFonts = REPO . "fonts/";
    private(set) static string $pathImages = REPO . "images/";
    private(set) static string $pathStyles = REPO . "styles/";
    private(set) static string $pathText = REPO . "text/";
    private(set) static string $pathEpubs = REPO . "epubs/";
    private(set) static string $pathEPUBCheck = ROOT . "/resources/EPUBCheck/";
    private(set) static string $pathPurpleRain = ROOT . "/resources/PurpleRain/";
    private(set) static array $memory = [];
    private static array $characterNonGrata = [
        ' ', '.', '\'', '"', ',', ':', ';', '!', '?', '(',
        ')', '[', ']', '{', '}', '&', '/', '\\', '’', '⊙',
        '🝄', '#', '<', '>', '='
    ];
    private(set) string $leaVersion {
        get => $this->leaVersion ??= self::comeToMe()->computeLeaVersion(minVersion: self::$minVersion);
    }
    private(set) string $leaNameShort {
        get => $this->leaNameShort ??= "Lea";
    }
    private(set) string $leaNamePlain {
        get => $this->leaNamePlain ??= self::comeToMe()->leaNameShort . " ePub anvil " . self::comeToMe()->leaVersion;
    }
    private(set) string $leaName {
        get => $this->leaName ??= Fancy::BOLD . self::comeToMe()->leaNameShort . " ePub anvil" . Fancy::UNBOLD . " " . self::comeToMe()->leaVersion;
    }
    private(set) array $doveCries = [];
    private static Affirmation $affirmation;

    /**
     * Private constructor: you don't create Girlfriends directly!
     */
    private function __construct()
    {
        self::$affirmation = new Affirmation();
    }

    /**
     * Summary of comeToMe:
     *
     * @return Girlfriend
     */
    public static function comeToMe(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Prevent cloning and unserializing: true Girlfriend exclusivity (don't let an exception fool you)!
     */
    private function __clone(): void // should be default for final classes
    {
    }

    public function __wakeup(): void
    {
        throw new BadMethodCallException(
            message: "Cannot unserialize exclusive instance of " . self::class . ", you ... singleton?"
        );
    }

    /**
     * Only one option for now, let's keep this simple.
     *
     * @param array $argv
     * @return void
     */
    public function parseArguments(array $argv): void
    {
        self::remember(name: "check-links", data: in_array(needle: "check-links", haystack: $argv) ? "yes" : "no");
        self::remember(name: "check-epub", data: in_array(needle: "check-epub", haystack: $argv) ? "yes" : "no");
    }

    /**
     * Concentrate on you is all I have to do.
     *
     * Purport:
     * With a proper emotional pump, you'll gain reliable internal focus.
     * I'll do this with my one and only Girlfriend.
     *
     * Some initialization.
     *
     * @return void
     */
    public function emotionalPump(): void
    {
        libxml_use_internal_errors(use_errors: true); // suppress all libxml warnings/errors
        libxml_clear_errors(); // clear any previous
    }

    /**
     * Use git describe to compute Lea version
     *
     * @param string $minVersion
     * @return string
     */
    private function computeLeaVersion(string $minVersion): string
    {
        $version = new Version(release: $minVersion, path: ROOT)->asString();
        return str_contains(haystack: $version, needle: "-g")
            ? "$version (dev build) [PHP " . phpversion() . "]"
            : $version;
    }

    /**
     * My name is Lea and I am funky
     * My name is Lea, the one and only
     *
     * Introduce Lea to the user.
     *
     * @return void
     */
    public function myNameIsLea(): void
    {
        echo PHP_EOL
            . Fancy::PURPLE_RAIN_INVERSE_WHITE . "[ " . Girlfriend::comeToMe()->leaName . " ]" . Fancy::RESET
            . PHP_EOL . PHP_EOL;

    }

    /**
     * Hey I got trouble
     * Somebody come check around me
     *
     * @return array
     */
    function whereAmI(): array
    {
        $trace = debug_backtrace(options: DEBUG_BACKTRACE_IGNORE_ARGS, limit: 1)[0];
        return array(
            "file" => $trace['file'],
            "line" => $trace['line']
        );
    }

    /**
     * Our unique Girlfriend can remember things for us.
     * Like the subfolder.
     *
     * @param string $name
     * @param string $data
     * @return void
     */
    public function remember(string $name, string $data): void
    {
        self::$memory["$name"] = $data;
    }

    /**
     * Our unique Girlfriend can recall things for us.
     * Like the default caption string for images.
     * By allowing her to stay silent, there won't be a need to throw exceptional tantrums.
     *
     * @param string $name
     * @return string
     */
    public function recall(string $name): string
    {
        return self::$memory["$name"] ?? "";
    }

    /**
     * This is what it sounds like
     * When doves cry
     *
     * When doves cry, something needs your attention.
     *
     * @param Ebook|Text $object
     * @param string $identifier
     * @param mixed ...$params
     * @return void
     * @throws Exception
     */
    public function makeDoveCry(Ebook|Text $object, string $identifier, ...$params): void
    {
        self::comeToMe()->doveCries[] = self::$affirmation->cry($object, $identifier, ...$params);
    }

    /**
     * Stop the doves from crying.
     *
     * @return void
     */
    public function silenceDoves(): void
    {
        self::comeToMe()->doveCries = [];
    }

    /**
     * Reads a file from storage into a string in memory
     * - Returns an empty string on read error
     *
     * @param string $fileName
     * @return string
     */
    #[NoDiscard]
    public function readFile(string $fileName): string
    {
        $content = @file_get_contents($fileName);
        return $content ?: "";
    }

    /**
     * Normalizes a title string for use in OEBPS/Text.
     * "The World That Couldn't Be by Clifford D. Simak"
     * => "TheWorldThatCouldntBeByCliffordDSimak.xhtml"
     *
     * @param string $title
     * @return string
     */
    public function strToEpubTextFileName(string $title): string
    {
        return str_replace(
                search: self::$characterNonGrata,
                replace: "",
                subject: ucwords($title)
            ) . ".xhtml";
    }

    /**
     * Normalizes a filename path string for use in OEBPS/Images.
     * "2025Q3-cover-512-QR.jpg"
     * => "2025q3-cover-512-qr.jpg"
     *
     * @param string $fileName
     * @return string
     */
    public function strToEpubImageFileName(string $fileName): string
    {
        $parts = pathinfo($fileName);
        return strtolower(
            string: str_replace(
                search: self::$characterNonGrata,
                replace: "-",
                subject: "lea-img-" . $parts['filename']
            ) . "." . $parts['extension']
        );
    }

    /**
     * Normalizes any string for use as identifiers.
     * "The World That Couldn't Be by Clifford D. Simak.xhtml"
     * => "the-world-that-couldn-t-be-by-clifford-d--simak-xhtml"
     *
     * @param string $string
     * @return string
     */
    public function strToEpubIdentifier(string $string): string
    {
        return str_replace(
            search: self::$characterNonGrata,
            replace: '-',
            subject: strtolower(trim($string))
        );
    }

    /**
     * Returns a subset of an array based on a preg match of array keys.
     *
     * @param string $pattern
     * @param array $array
     * @param int $flags
     * @return array
     */
    function arrayPregKeys(string $pattern, array $array, int $flags = 0): array
    {
        return array_filter(
            array: $array,
            callback: function ($key) use ($pattern, $flags) {
                return preg_match(pattern: $pattern, subject: $key, flags: $flags);
            },
            mode: ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Extraordinary
     * The way you make me feel
     * I'm so very glad it's real
     * And not a dream
     *
     * We're taking the red pill and are returning to the real world now.
     * Right now.
     *
     * @param Throwable $throwable
     * @return never
     */
    public function extraordinary(Throwable $throwable): never
    {
        echo "Oh, I just had an oopsie..." . PHP_EOL
            . "If you'd be so kind as to help a damsel in distress," . PHP_EOL
            . "would you mind getting back to my creator and tell him:" . PHP_EOL
            . "\"" . Fancy::PURPLE_RAIN_BOLD_INVERSE_WHITE . $throwable->getMessage()
            . " in " . basename($throwable->getFile())
            . " on line " . $throwable->getLine() . Fancy::RESET . "\"" . PHP_EOL
            . "You know, I'm just a girl; I can't do simple things." . PHP_EOL;
        exit;
    }

    /**
     * Executes the final ePub validation process using epubcheck.jar and returns the result.
     *
     * @param string $fileName The path to the EPUB file to be checked.
     * @return array An associative array containing the following keys:
     *               - "stdout": Output captured from the standard output of the process.
     *               - "stderr": Output captured from the standard error of the process.
     *               - "return": The exit code returned by the process.
     */
    public function checkEpub(string $fileName): array
    {
        $cmd = "java -jar " . Girlfriend::$pathEPUBCheck . "epubcheck.jar '$fileName' --version";
        $descriptors = [
            1 => ['pipe', 'w'],     // stdout
            2 => ['pipe', 'w'],     // stderr
        ];
        $process = proc_open($cmd, $descriptors, $pipes);
        $stdout = $stderr = $returnCode = null;
        if (is_resource($process)) {
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $returnCode = proc_close($process);
        }
        return [
            "stdout" => $stdout,
            "stderr" => $stderr,
            "return" => $returnCode
        ];
    }
}
