<?php

namespace DataType\Exception;

class ClassInvalidException extends DataTypeLogicException
{
    const CLASS_DOESNT_EXIST_MESSAGE = 'Class "%s" does not exist';

    const CLASS_IS_NOT_ENUM = 'Class "%s" is not enum';

    public static function classNotFound(string $typeString): self
    {
        $message = sprintf(self::CLASS_DOESNT_EXIST_MESSAGE, $typeString);
        return new self($message);
    }

    public static function classIsNotEnum(string $typeString): self
    {
        $message = sprintf(self::CLASS_IS_NOT_ENUM, $typeString);
        return new self($message);
    }
}
