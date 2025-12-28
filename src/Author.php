<?php

declare(strict_types=1);

namespace Lea;

// Author class

final class Author
{
    public string $name = "" {
        get => $this->name;
        set(string $value) => trim(string: $value);
    }
    public string $fileAs = "" {
        get => $this->fileAs;
        set(string $value) => trim(string: $value);
    }
    /**
     * Summary of __construct
     * @param string $name
     * @param string $fileAs
     */
    public function __construct(string $name = "", string $fileAs = "")
    {
        $this->name = $name;
        $this->fileAs = $fileAs;
    }
}
