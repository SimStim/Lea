<?php

declare(strict_types=1);

namespace Lea;

define(constant_name: 'ROOT', value: dirname(path: __DIR__) . "/Lea");
require_once ROOT . "/vendor/autoload.php";

define(constant_name: 'REPO', value: ROOT . "/examples");
$annaStesia = Girlfriend::comeToMe();
define(constant_name: "NAME", value: "Lea: ePub anvil, version " . $annaStesia->leaVersion);

echo NAME . PHP_EOL;

$work = new PaisleyPark(ebookConfigFile: "aaa");
var_dump($work->cream(ebookConfigFile: "bbb"));
var_dump(value: $work->ebook->texts->top()->fileName);
var_dump(value: $work->ebook->texts->top()->title);
var_dump(value: $work->ebook->texts->top()->author);
var_dump(value: $work->ebook->texts->top()->blurb);
var_dump($work->segue());
var_dump($work->theOpera());
echo Girlfriend::comeToMe()::$fallout;
