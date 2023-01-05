<?php

declare(strict_types = 1);

namespace DataType\Exception;

use DataType\InputType;
use DataType\Messages;

class DataTypeDefinitionException extends DataTypeException
{
    public static function foundNonPropertyDefinition(int $index, string $classname): self
    {
        $message = sprintf(
            Messages::MUST_RETURN_ARRAY_OF_PROPERTY_DEFINITION,
            $classname,
            InputType::class,
            $index
        );

        return new self($message);
    }
}
