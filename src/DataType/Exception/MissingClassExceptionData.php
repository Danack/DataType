<?php

declare(strict_types = 1);

namespace DataType\Exception;

use DataType\Messages;

class MissingClassExceptionData extends DataTypeException
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
