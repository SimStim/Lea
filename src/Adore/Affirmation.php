<?php

declare(strict_types=1);

namespace Lea\Adore;

use Exception;
use Lea\Domain\Ebook;
use Lea\Domain\Text;

/**
 * I trust you're having a quick and enjoyable adjustment period
 * As you can see, we are communicating now telepathically
 */
final class Affirmation
{
    public function __construct()
    {
    }

    /**
     * @param Ebook|Text $object
     * @param Controversy $identifier
     * @param mixed ...$params
     * @return DoveCry
     * @throws Exception
     */
    public function cry(Ebook|Text $object, Controversy $identifier, ...$params): DoveCry
    {
        $message = $identifier->message();
        if (preg_match_all(pattern: "/#\d#/", subject: $message["message"] . $message["suggestion"]) !== count($params))
            throw new Exception(
                message: "Parameter count does not match required number of placeholders for message '$identifier->name'"
            );
        foreach ($params as $key => $value) {
            $message["message"] = str_replace(
                search: "#" . $key + 1 . "#",
                replace: $value,
                subject: $message["message"]
            );
            $message["suggestion"] = str_replace(
                search: "#" . $key + 1 . "#",
                replace: $value,
                subject: $message["suggestion"]
            );
        }
        return new DoveCry($object, $message["flaw"], $message["message"], $message["suggestion"]);
    }
}
