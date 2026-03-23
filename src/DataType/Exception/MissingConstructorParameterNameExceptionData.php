<?php

declare(strict_types=1);


namespace DataType\Exception;

use DataType\Messages;

/**
 * The object-class that the code is trying to create has a parameter
 * for which no value is available.
 */
class MissingConstructorParameterNameExceptionData extends DataTypeLogicException
{
    public static function missingParam(string $classname, string $param_name): self
    {
        $message = sprintf(
            Messages::MISSING_PARAMETER_NAME,
            $classname,
            $param_name
        );

        return new self($message);
    }
}
