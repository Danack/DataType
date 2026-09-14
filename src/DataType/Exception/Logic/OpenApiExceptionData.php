<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

/**
 * Thrown when OpenAPI schema/description generation encounters invalid state.
 */
class OpenApiExceptionData extends DataTypeLogicException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
