<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetIntOrDefault;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Integer input with a default when the parameter is missing.
 */
#[\Attribute]
class BasicIntegerOrDefault implements HasInputType
{
    public function __construct(
        private string $name,
        private int|null $default
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetIntOrDefault($this->default),
        );
    }
}
