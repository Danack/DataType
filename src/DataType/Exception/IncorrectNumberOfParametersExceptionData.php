<?php

declare(strict_types=1);


namespace DataType\Exception;

use DataType\Messages;

class IncorrectNumberOfParametersExceptionData extends DataTypeException
{
    public static function wrongNumber(string $classname, int $expected, int $available): self
    {
        $message = sprintf(
            Messages::INCORRECT_NUMBER_OF_PARAMETERS,
            $classname,
            $expected,
            $available
        );

        return new self($message);
    }
}
