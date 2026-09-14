<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

/**
 * Thrown when an array is expected to contain only ValidationProblem instances.
 */
class OnlyValidationProblemsAllowedException extends DataTypeLogicException
{
    public const MESSAGE = "Array must contain only 'ValidationProblem's instead got [%s]";

    /**
     * @param mixed $wrongType
     */
    public function __construct($wrongType)
    {
        parent::__construct(sprintf(self::MESSAGE, gettype($wrongType)));
    }
}
