<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetFloatOrDefault;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Float input with a default when the parameter is missing.
 */
#[\Attribute]
class FloatOrDefault implements HasInputType
{
    public function __construct(
        private string $name,
        private float|null $default
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetFloatOrDefault($this->default),
        );
    }
}
