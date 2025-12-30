<?php

declare(strict_types=1);

namespace Lea\Domain;

// Author class

final class Author
{
    public function __construct(
        public string $name = "" { set(string $value) => trim(string: $value); },
        public string $fileAs = "" { set(string $value) => trim(string: $value); }
    ) {}
}
