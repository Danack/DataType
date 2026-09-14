<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

/**
 * Thrown when preg_match fails unexpectedly.
 */
class PregMatchFailedException extends DataTypeLogicException
{
    public function __construct(string $message = "preg_match failed")
    {
        parent::__construct($message);
    }

    public static function forPattern(string $pattern): self
    {
        return new self("preg_match failed for pattern: " . $pattern);
    }
}
