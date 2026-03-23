<?php

declare(strict_types = 1);

namespace DataType\Exception;

use DataType\Messages;

/**
 * Thrown when configured datetime format values are invalid (non-string).
 */
class InvalidDatetimeFormatExceptionData extends DataTypeLogicException
{
    /**
     * Only strings are allowed datetime format.
     * @param int $index
     * @param mixed $nonStringVariable
     * @return InvalidDatetimeFormatExceptionData
     */
    public static function stringRequired(int $index, $nonStringVariable): self
    {
        $message = sprintf(
            Messages::ERROR_DATE_FORMAT_MUST_BE_STRING,
            gettype($nonStringVariable),
            $index
        );

        return new self($message);
    }
}
