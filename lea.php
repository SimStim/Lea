<?php

declare(strict_types=1);

namespace Lea;

use Lea\Adore\Girlfriend;

define(constant_name: 'ROOT', value: dirname(path: __DIR__) . "/Lea");
define(constant_name: 'REPO', value: ROOT . "/examples/");
require_once ROOT . "/vendor/autoload.php";

Girlfriend::comeToMe()->emotionalPump();
Girlfriend::comeToMe()->myNameIsLea();

$work = new PaisleyPark(fileName: $argv[1] ?? "");
$work->segue();
if (!$work->inThisBedEyeScream()) exit;
$work->seguePartTwo();
var_dump($work->theOpera());
