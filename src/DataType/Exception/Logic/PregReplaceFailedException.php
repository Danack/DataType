<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

/**
 * Thrown when preg_replace fails unexpectedly.
 */
class PregReplaceFailedException extends DataTypeLogicException
{
    public static function forPattern(string $pattern): self
    {
        return new self("preg_replace failed for pattern: " . $pattern);
    }

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
