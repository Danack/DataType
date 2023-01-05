<?php

declare(strict_types=1);


namespace DataType\Exception;

use DataType\Messages;

class MissingConstructorParameterNameExceptionData extends DataTypeException
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
