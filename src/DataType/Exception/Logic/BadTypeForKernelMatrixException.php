<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

use DataType\Messages;

/**
 * Thrown when a kernel matrix extract rule receives a non-string value.
 */
class BadTypeForKernelMatrixException extends DataTypeLogicException
{
    public function __construct()
    {
        parent::__construct(Messages::BAD_TYPE_FOR_KERNEL_MATRIX_PROCESS_RULE);
    }
}
