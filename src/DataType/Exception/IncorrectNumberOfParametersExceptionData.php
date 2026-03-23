<?php

declare(strict_types=1);


namespace DataType\Exception;

use DataType\Messages;

/**
 * Thrown when the number of resolved constructor arguments does not match
 * the target class constructor parameter count.
 */
class IncorrectNumberOfParametersExceptionData extends DataTypeLogicException
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
