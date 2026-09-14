<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

use DataType\Messages;

/**
 * Thrown when a minutes offset is configured with a negative value.
 */
class MinutesMustBeGreaterThanZeroException extends DataTypeLogicException
{
    public function __construct()
    {
        parent::__construct(Messages::MINUTES_MUST_BE_GREATER_THAN_ZERO);
    }
}
