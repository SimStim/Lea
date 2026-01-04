<?php

declare(strict_types=1);

namespace Lea\Domain;

use NoDiscard;

/**
 * ISBN domain class
 */
final class ISBN
{
    public function __construct(
        private(set) string $isbn {
            set {
                $this->isbn = $this->trimISBN($value);
            }
        }
    )
    {
    }

    /**
     * Trims a passed string with an ISBN-13:
     * - removes all dashes
     * - trims white space
     *
     * @param string $isbn
     * @return string
     */
    #[NoDiscard]
    private function trimISBN(string $isbn): string
    {
        return trim(string: str_replace(search: "-", replace: "", subject: $isbn));
    }

    /**
     * Checks a passed string for conformity with ISBN-13 specs
     *
     * @param string $isbn
     * @return bool
     * TODO: actually do something useful, like check the checksum and stuff
     */
    #[NoDiscard]
    public function isWellFormed(string $isbn): bool
    {
        $isbn = $this->trimISBN($isbn);
        return mb_strlen(string: $isbn) === 13
            && is_numeric(value: $isbn);
    }
}
