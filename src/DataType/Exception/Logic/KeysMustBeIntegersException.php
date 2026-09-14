<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

/**
 * Thrown when array keys are expected to be integers.
 */
class KeysMustBeIntegersException extends DataTypeLogicException
{
    public const MESSAGE = "Key for array must be integer";

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
