<?php

declare(strict_types=1);

namespace Lea;

// ISBN class

final class ISBN
{
    public string $isbn = "" {
        get => $this->isbn;
        set(string $value) {
            $value = $this->trimISBN(isbn: $value);
            $this->isbn = ($this->isWellFormed(isbn: $value) ? $value : "");
        }
    }
    /**
     * Summary of __construct
     * @param string $isbn
     */
    public function __construct(string $isbn = "")
    {
        $this->isbn = $isbn;
    }

    /**
     * Summary of trimISBN
     * @param string $isbn
     * @return string
     */
    private function trimISBN(string $isbn): string
    {
        return trim(string: str_replace(search: "-", replace: "", subject: $isbn));
    }

    /**
     * Summary of isWellFormed
     * @param string $isbn
     * @return bool
     * TODO: actually do something useful, like check the checksum and stuff
     */
    private function isWellFormed(string $isbn): bool
    {
        return mb_strlen(string: $isbn) === 13
            && is_numeric(value: $isbn);
    }
}
