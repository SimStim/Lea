<?php

use SebastianBergmann\Version;

function leaVersion(string $minVersion = "0.0.1"): string
{
    $version = new Version(release: $minVersion, path: ROOT)->asString();
    $version =  (str_contains($version, '-g'))
        ? "$version (dev build)"
        :  explode("-", $version)[0];
    return $version;
}
