<?php

declare(strict_types = 1);

namespace DataType\Exception;

use DataType\Messages;

/**
 * Thrown when a referenced class name cannot be found.
 */
class MissingClassExceptionData extends DataTypeLogicException
{
    public static function fromClassname(
        string $classname,
        \ReflectionException|null $re = null
    ): self {
        $message = sprintf(
            Messages::CLASS_NOT_FOUND,
            $classname
        );

        $code = 0;
        if ($re !== null) {
            $code = $re->getCode();
        }

        return new self($message, $code, $re);
    }
}
