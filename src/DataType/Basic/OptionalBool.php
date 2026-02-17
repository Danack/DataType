<?php

declare(strict_types = 1);

namespace DataType\Basic;

use DataType\ExtractRule\GetBoolOrDefault;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Property type for an optional boolean with a configurable default.
 * Uses DataType\ExtractRule\GetBoolOrDefault - accepts "true", "false" as string values.
 */
#[\Attribute]
class OptionalBool implements HasInputType
{
    public function __construct(
        private string $name,
        private readonly bool $default = false
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetBoolOrDefault($this->default),
        );
    }
}
