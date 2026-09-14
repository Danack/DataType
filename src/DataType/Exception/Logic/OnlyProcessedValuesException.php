<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

/**
 * Thrown when an array contains values that are not ProcessedValue instances.
 */
class OnlyProcessedValuesException extends DataTypeLogicException
{
    public const MESSAGE = "Processed values must all be instances of ProcessedValue.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
