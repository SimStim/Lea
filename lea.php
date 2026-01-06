<?php

declare(strict_types=1);

namespace Lea;

define(constant_name: 'ROOT', value: dirname(path: __DIR__) . "/Lea");
define(constant_name: 'REPO', value: ROOT . "/examples");
require_once ROOT . "/vendor/autoload.php";
Girlfriend::emotionalPump();

echo Girlfriend::comeToMe()->leaName . PHP_EOL;
$work = new PaisleyPark(fileName: "tpsf-8.xml");
if ($work->segue() === false) exit;
echo implode(
        separator: PHP_EOL,
        array: array_slice(
            array: explode(
                separator: PHP_EOL,
                string: Girlfriend::comeToMe()::$fallout
            ),
            offset: 0,
            length: 10
        )
    ) . PHP_EOL;
// var_dump($work);
