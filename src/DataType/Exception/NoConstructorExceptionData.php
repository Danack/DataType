<?php

declare(strict_types=1);


namespace DataType\Exception;

use DataType\Messages;

/**
 * The object-class that the code is trying to create has no
 * constructor
 */
class NoConstructorExceptionData extends DataTypeLogicException
{
    public static function noConstructor(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_LACKS_CONSTRUCTOR,
            $classname
        );

        return new self($message);
    }

    public static function notPublicConstructor(string $classname): self
    {
        $message = sprintf(
            Messages::CLASS_LACKS_PUBLIC_CONSTRUCTOR,
            $classname
        );

        return new self($message);
    }
}
