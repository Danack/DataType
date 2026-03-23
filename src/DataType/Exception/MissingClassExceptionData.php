<?php

declare(strict_types = 1);

namespace DataType\Exception;

use DataType\Messages;

/**
 * Thrown when a referenced class name cannot be found.
 */
class MissingClassExceptionData extends DataTypeLogicException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_NOT_FOUND,
            $classname
        );

        return new self($message);
    }
}
