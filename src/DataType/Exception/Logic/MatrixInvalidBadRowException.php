<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

use DataType\Messages;

/**
 * Thrown when a kernel matrix default contains a non-array row.
 */
class MatrixInvalidBadRowException extends DataTypeLogicException
{
    public function __construct()
    {
        parent::__construct(Messages::MATRIX_INVALID_BAD_ROW);
    }
}
