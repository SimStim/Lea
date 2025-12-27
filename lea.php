<?php

namespace Lea;

define(
    constant_name: 'ROOT',
    value: dirname(__DIR__) . "/Lea"
);

require_once ROOT . "/inc/ancillary.php";
require_once ROOT . "/vendor/autoload.php";

define(
    constant_name: "NAME",
    value: "Lea: ePub anvil, version " . leaVersion(minVersion: "0.0.4") . " [PHP " . phpversion() . "]"
);

echo NAME;
