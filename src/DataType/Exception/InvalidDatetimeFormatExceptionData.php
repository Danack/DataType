<?php

declare(strict_types = 1);

namespace DataType\Exception;

use DataType\Messages;

class InvalidDatetimeFormatExceptionData extends \DataType\Exception\DataTypeException
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
