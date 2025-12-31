<?php

declare(strict_types=1);

namespace Lea\Domain;

/**
 * ISBN domain class
 */
final class ISBN
{
    public function __construct(
        public string $isbn = "" {
            set {
                $value = $this->trimISBN(isbn: $value);
                $this->isbn = ($this->isWellFormed(isbn: $value) ? $value : "");
            }
        }
    )
    {
    }

    /**
     * @param string $isbn
     * @return string
     */
    private function trimISBN(string $isbn): string
    {
        return trim(string: str_replace(search: "-", replace: "", subject: $isbn));
    }

    /**
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
