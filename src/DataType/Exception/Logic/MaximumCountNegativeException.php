<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

use DataType\Messages;

/**
 * Thrown when MaximumCount is constructed with a negative count.
 */
class MaximumCountNegativeException extends DataTypeLogicException
{
    public function __construct()
    {
        parent::__construct(Messages::ERROR_MAXIMUM_COUNT_MINIMUM);
    }
}
