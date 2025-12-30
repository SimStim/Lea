<?php

declare(strict_types=1);

namespace Lea;

use BadMethodCallException;
use SebastianBergmann\Version;

//* You can do it because I'm your friend.

final class Girlfriend
{
    private static ?self $instance = null;

    private function __construct()
    {
        // Private constructor: you don't create Girlfriends directly!
    }

    private(set) string $leaVersion {
        get => $this->leaVersion ??= $this->computeLeaVersion(minVersion: "0.0.8");
    }

    /**
     * Summary of comeToMe
     * @return Girlfriend
     */
    public static function comeToMe(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Prevent cloning and unserializing: true Girlfriend exclusivity (don't let an exception fool you)!
     */
    private function __clone(): void
    {
    } // should be default for final classes

    public function __wakeup(): void
    {
        throw new BadMethodCallException(
            message: "Cannot unserialize exclusive instance of " . self::class . ", you ... singleton?"
        );
    }

    /**
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
}
