<?php

declare(strict_types=1);

namespace Lea;

define(
    constant_name: 'ROOT',
    value: dirname(__DIR__) . "/Lea"
);

require_once ROOT . "/vendor/autoload.php";

use Lea\Ancillary;

define(
    constant_name: "NAME",
    value: "Lea: ePub anvil, version " . Ancillary::leaVersion(minVersion: "0.0.4") . " [PHP " . phpversion() . "]"
);

echo NAME . PHP_EOL;
