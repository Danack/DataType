<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

use DataType\Messages;

/**
 * Thrown when MinimumCount is constructed with a negative count.
 */
class MinimumCountNegativeException extends DataTypeLogicException
{
    public function __construct()
    {
        parent::__construct(Messages::ERROR_MINIMUM_COUNT_MINIMUM);
    }
}
