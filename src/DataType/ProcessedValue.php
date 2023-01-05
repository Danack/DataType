<?php

declare(strict_types = 1);

namespace DataType;

/**
 * Holds a processed value and the source InputType that was used to process it.
 *
 * This allows for subsequent processing to reference earlier ones. e.g. checking
 * a password has been entered the same twice.
 */
class ProcessedValue
{
    public function __construct(
        private InputType $inputType,
        private mixed     $value
    ) {
    }

    public function getInputType(): InputType
    {
        return $this->inputType;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
