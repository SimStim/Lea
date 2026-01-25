<?php

declare(strict_types=1);

namespace Lea;

use Lea\Adore\Fancy;
use Lea\Adore\Girlfriend;

define(constant_name: 'ROOT', value: dirname(path: __DIR__) . "/Lea");
define(constant_name: 'REPO', value: ROOT . "/examples");
require_once ROOT . "/vendor/autoload.php";
Girlfriend::emotionalPump();

echo PHP_EOL . Fancy::PURPLE_RAIN_BOLD_INVERSE . Girlfriend::comeToMe()->leaName . Fancy::RESET . PHP_EOL . PHP_EOL;

$work = new PaisleyPark(fileName: "tpsf-8.xml");
$work->segue();
var_dump(Girlfriend::comeToMe()->doveCries);
