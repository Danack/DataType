<?php

declare(strict_types = 1);

namespace DataType\Exception;

use DataType\InputType;
use DataType\Messages;

/**
 * Thrown when a DataType class has an invalid static definition.
 *
 * Currently this is raised when a class's input type list contains values
 * that are not `InputType` instances.
 */
class DataTypeDefinitionException extends DataTypeLogicException
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
