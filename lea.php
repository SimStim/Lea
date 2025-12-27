<?php

require_once __DIR__ . '/vendor/autoload.php';

use SebastianBergmann\Version;

$version = new Version('0.0.1', __DIR__);
echo "Lea: ePub anvil v" . $version->asString() . PHP_EOL;
