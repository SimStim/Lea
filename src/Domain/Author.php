<?php

declare(strict_types=1);

namespace Lea\Domain;

/**
 * Author domain class
 */
final class Author
{
    public function __construct(
        public string $name = "" {
            set => trim(string: $value);
        },
        public string $fileAs = "" {
            set => trim(string: $value);
        }
    )
    {
    }
}
