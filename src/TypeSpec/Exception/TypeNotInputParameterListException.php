<?php

declare(strict_types = 1);

namespace TypeSpec\Exception;

use TypeSpec\Messages;
use TypeSpec\HasDataTypeList;

class TypeNotInputParameterListException extends TypeSpecException
{
    public static function fromClassname(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_MUST_IMPLEMENT_INPUT_PARAMETER,
            $classname,
            HasDataTypeList::class
        );

        return new self($message);
    }
}
