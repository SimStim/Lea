<?php

declare(strict_types=1);

namespace Lea;

use SebastianBergmann\Version;

final class Ancillary
{
    public static function leaVersion(string $minVersion = "0.0.1"): string
    {
        $version = new Version(release: $minVersion, path: ROOT)->asString();
        return $version . (str_contains($version, '-g') ? " (dev build)" : "");
    }
}
