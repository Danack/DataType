<?php

declare(strict_types=1);

namespace DataType\Exception;

/**
 * Indicates programmer error in the creation of the DataTypes, which can't be handled locally in code.
 *
 * e.g. if you extract a value as a string, and then have a ProcessRule that requires an integer,
 * a subtype of DataTypeLogicException will be thrown.
 *
 * @unchecked
 */
abstract class DataTypeLogicException extends \Exception
{
    protected function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
