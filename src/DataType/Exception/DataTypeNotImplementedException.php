<?php

declare(strict_types = 1);

namespace DataType\Exception;

use DataType\Messages;
use DataType\DataType;

class DataTypeNotImplementedException extends DataTypeException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_MUST_IMPLEMENT_DATATYPE_INTERFACE,
            $classname,
            DataType::class
        );

        return new self($message);
    }
}
