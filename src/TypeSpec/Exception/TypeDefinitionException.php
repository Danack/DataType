<?php

declare(strict_types = 1);

namespace TypeSpec\Exception;

use TypeSpec\DataType;
use TypeSpec\Messages;

class TypeDefinitionException extends TypeSpecException
{
    public static function foundNonPropertyDefinition(int $index, string $classname): self
    {
        $message = sprintf(
            Messages::MUST_RETURN_ARRAY_OF_PROPERTY_DEFINITION,
            $classname,
            DataType::class,
            $index
        );

        return new self($message);
    }
}
