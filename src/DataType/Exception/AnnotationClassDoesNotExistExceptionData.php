<?php

declare(strict_types=1);


namespace DataType\Exception;

use DataType\Messages;

/**
 * Thrown when a property references an annotation/attribute class name
 * that does not exist or cannot be autoloaded.
 */
class AnnotationClassDoesNotExistExceptionData extends DataTypeLogicException
{
    public static function create(
        string $classname,
        string $property_name,
        string $annotation_name
    ): self {
        $message = sprintf(
            Messages::PROPERTY_ANNOTATION_DOES_NOT_EXIST,
            $property_name,
            $classname,
            $annotation_name
        );

        return new self($message);
    }
}
