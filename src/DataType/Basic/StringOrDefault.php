<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetStringOrDefault;
use DataType\HasInputType;
use DataType\InputType;

/**
 * String input with a default when the parameter is missing.
 */
#[\Attribute]
class StringOrDefault implements HasInputType
{
    public function __construct(
        private string $name,
        private string|null $default
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrDefault($this->default),
        );
    }
}
