<?php

declare(strict_types=1);

namespace Lea\Domain;

// ISBN class

final class ISBN
{
    public function __construct(
        public string $isbn = "" { set(string $value) {
                $value = $this->trimISBN(isbn: $value);
                $this->isbn = ($this->isWellFormed(isbn: $value) ? $value : "");
            } }
    ) {}

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
