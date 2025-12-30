<?php

declare(strict_types=1);

namespace Lea;

define(constant_name: 'ROOT', value: dirname(path: __DIR__) . "/Lea");
require_once ROOT . "/vendor/autoload.php";

$annaStesia = Girlfriend::comeToMe();
define(constant_name: "NAME", value: "Lea: ePub anvil, version " . $annaStesia->leaVersion);

echo NAME . PHP_EOL;

$work = new PaisleyPark(ebookConfigFile: "aaa");
$result = $work->cream(ebookConfigFile: "bbb");
echo "Creamed result = ";
var_dump(value: $result);
echo PHP_EOL;
var_dump(value: $work);
var_dump($work->segue());
var_dump($work->theOpera());
