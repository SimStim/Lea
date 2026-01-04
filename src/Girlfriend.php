<?php

declare(strict_types=1);

namespace Lea;

use BadMethodCallException;
use NoDiscard;
use SebastianBergmann\Version;

/**
 * You can do it because I'm your friend.
 */
final class Girlfriend
{
    private static ?self $instance = null;
    private static string $minVersion = "0.0.12";
    private(set) static string $fallout = "";

    private function __construct() // Private constructor: you don't create Girlfriends directly!
    {
    }

    private(set) string $leaVersion {
        get => $this->leaVersion ??= $this->computeLeaVersion(minVersion: self::$minVersion);
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
     * @param string $fileName
     * @return string
     */
    #[NoDiscard]
    public function readFileOrDie(string $fileName): string
    {
        $content = file_get_contents($fileName);
        if ($content === false)
            self::collectFallout("Failed to load file: $fileName.");
        return $content ?: "";
    }

    /**
     * Collect all the error messages to be presented to the user in one fell swoop
     *
     * @param string $message
     * @return void
     */
    public function collectFallout(string $message): void
    {
        self::$fallout .= $message . PHP_EOL;
    }
}
