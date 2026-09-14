<?php

declare(strict_types=1);

namespace DataType\Exception\Logic;

use DataType\Exception\DataTypeLogicException;

use DataType\Messages;

/**
 * Thrown when a count process rule is given a non-array value.
 */
class WrongTypeForCountRuleException extends DataTypeLogicException
{
    public static function forMaximumCount(mixed $value): self
    {
        $message = sprintf(
            Messages::ERROR_WRONG_TYPE_VARIANT_1,
            gettype($value)
        );

        return new self($message);
    }

    public static function forMinimumCount(mixed $value): self
    {
        $message = sprintf(
            Messages::ERROR_WRONG_TYPE,
            gettype($value)
        );

        return new self($message);
    }

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
