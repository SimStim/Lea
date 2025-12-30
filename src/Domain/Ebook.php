<?php

declare(strict_types=1);

namespace Lea\Domain;

use SplDoublyLinkedList;

// Text class

final class Ebook
{
    public function __construct(
        public string                             $title = "" {
            set => trim($value);
        },
        public Author                             $author = new Author(),
        public ISBN                               $isbn = new ISBN(),
        private(set) readonly SplDoublyLinkedList $texts = new SplDoublyLinkedList()
    )
    {
    }

    /**
     * @param Text $text
     * @return void
     */
    public function addText(Text $text): void
    {
        $this->texts->push(value: $text);
    }
}
