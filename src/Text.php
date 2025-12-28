<?php

declare(strict_types=1);

namespace Lea;

// Text class

final class Text
{
    public string $title = "" {
        get => $this->title;
        set(string $value) => $value;
    }
    public Author $author {
        get => $this->author ??= new Author();
        set(Author  $object) => $object;
    }
    /**
     * Summary of __construct
     * @param string $title
     * @param Author $author
     */
    public function __construct(string $title = "", Author $author = new Author())
    {
        $this->title = $title;
        $this->author = $author;
    }
}
