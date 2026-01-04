<?php

declare(strict_types=1);

namespace Lea;

define(constant_name: 'ROOT', value: dirname(path: __DIR__) . "/Lea");
require_once ROOT . "/vendor/autoload.php";

define(constant_name: 'REPO', value: ROOT . "/examples");
define(constant_name: "NAME", value: "Lea: ePub anvil, version " . Girlfriend::comeToMe()->leaVersion);

echo NAME . PHP_EOL;

$work = new PaisleyPark(fileName: "tpsf-8.xml");
var_dump(value: $work->ebook->fileName);
var_dump(value: $work->ebook->title);
var_dump(value: $work->ebook->isbn);
var_dump(value: $work->ebook->authors);
var_dump(value: $work->ebook->texts);
echo Girlfriend::comeToMe()::$fallout;
