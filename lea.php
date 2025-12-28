<?php

declare(strict_types=1);

namespace Lea;

define(constant_name: 'ROOT', value: dirname(path: __DIR__) . "/Lea");
require_once ROOT . "/vendor/autoload.php";

$annaStesia = Girlfriend::comeToMe();
define(constant_name: "NAME", value: "Lea: ePub anvil, version " . $annaStesia->leaVersion);

echo NAME . PHP_EOL;
$isbn = new ISBN(isbn: "978-9908972633");
var_export(value: $isbn) . PHP_EOL;
$author = new Author(name: "Idoru Toei", fileAs: "Pech, Eduard [Idoru Toei]");
var_export(value: $author) . PHP_EOL;
$text = new Text(title: "Hubris", author: $author);
var_export(value: $text) . PHP_EOL;
