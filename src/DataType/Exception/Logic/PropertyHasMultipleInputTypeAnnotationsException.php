<?php

declare(strict_types=1);


namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

use DataType\Messages;

/**
 * Thrown when a single property declares more than one InputType annotation.
 */
class PropertyHasMultipleInputTypeAnnotationsException extends DataTypeLogicException
{
    public static function create(string $classname, string $property_name): self
    {
        $message = sprintf(
            Messages::PROPERTY_MULTIPLE_INPUT_TYPE_SPEC,
            $property_name,
            $classname
        );

        return new self($message);
    }
}
