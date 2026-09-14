<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

/**
 * Thrown when processed values are expected to have string keys.
 */
class KeysMustBeStringsException extends DataTypeLogicException
{
    public const MESSAGE = "Processed values must have string keys";

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
