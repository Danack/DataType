<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

/**
 * Thrown when accessing a processed value that is not present.
 */
class MissingValueException extends DataTypeLogicException
{
    public const MESSAGE = "Trying to access [%s] which isn't present in ParamValuesImpl.";

    public function __construct(string $name)
    {
        parent::__construct(sprintf(self::MESSAGE, $name));
    }
}
