<?php

declare(strict_types=1);

namespace Lea\Domain;

use SplDoublyLinkedList;

// Text class

final class Ebook
{
    public function __construct(
        public string $title = "" { set(string $value) => trim($value); },
        public Author $author = new Author(),
        public ISBN $isbn = new ISBN(),
        public private(set) SplDoublyLinkedList $texts = new SplDoublyLinkedList()
    ) {}
    public function addText(Text $text): void
    {
        $this->texts->push(value: $text);
    }
}
