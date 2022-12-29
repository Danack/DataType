<?php

declare(strict_types = 1);

namespace TypeSpec;

/**
 * Holds a processed value and the source InputType that was used to process it.
 *
 * This allows for subsequent processing to reference earlier ones. e.g. checking
 * a password has been entered the same twice.
 */
class ProcessedValue
{
    public function __construct(
        private DataType $dataType,
        private mixed    $value
    ) {
    }

    public function getDataType(): DataType
    {
        return $this->dataType;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
