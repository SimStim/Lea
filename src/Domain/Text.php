<?php

declare(strict_types=1);

namespace Lea\Domain;

// Text class

final class Text
{
    public function __construct(
        public string $title = "" { set(string $value) => trim(string: $value); },
        public Author $author = new Author() { set(Author  $object) => $object; },
        public string $blurb = "" { set(string $value) => trim(string: $value); }
    ) {}
}
