<?php

declare(strict_types=1);

namespace Lea\Adore;

use NoDiscard;
use BadMethodCallException;
use SebastianBergmann\Version;

/**
 * You can do it because I'm your friend.
 */
final class Girlfriend
{
    private static ?self $instance = null;
    private static string $minVersion = "0.0.16";
    private(set) static string $pathEbooks = REPO . "/configs/ebooks/";
    private(set) static string $pathImprints = REPO . "/configs/imprints/";
    private(set) static string $pathScripts = REPO . "/configs/scripts/";
    private(set) static string $pathFonts = REPO . "/fonts/";
    private(set) static string $pathImages = REPO . "/images/";
    private(set) static string $pathStyles = REPO . "/styles/";
    private(set) static string $pathText = REPO . "/text/";
    private(set) string $leaVersion {
        get => $this->leaVersion ??= $this->computeLeaVersion(minVersion: self::$minVersion);
    }
    private(set) string $leaName {
        get => $this->leaName ??= Fancy::BOLD . "Lea ePub anvil" . Fancy::UNBOLD . " " . self::comeToMe()->leaVersion;
    }
    private(set) array $doveCries = [];

    /**
     * Private constructor: you don't create Girlfriends directly!
     */
    private function __construct()
    {
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
     * Concentrate on you is all I have to do.
     *
     * Purport:
     * With proper emotional pump, you'll gain reliable internal focus.
     * I'll do this with my one and only Girlfriend.
     *
     * Some initialization.
     *
     * @return void
     */
    public static function emotionalPump(): void
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
    private static function computeLeaVersion(string $minVersion): string
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
     * This is what it sounds like
     * When doves cry
     *
     * When doves cry, something needs your attention.
     *
     * @param DoveCry $doveCry
     * @return void
     */
    public function makeDoveCry(DoveCry $doveCry): void
    {
        self::comeToMe()->doveCries[] = $doveCry;
    }

    /**
     * Reads a file from storage into a string in memory
     * - returns an empty string on read error
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
}
