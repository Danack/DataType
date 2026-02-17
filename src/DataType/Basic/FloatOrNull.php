<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetFloatOrNull;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Required parameter that may be null. When the value is present it must be a float; when null, the property receives null.
 */
#[\Attribute]
class FloatOrNull implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetFloatOrNull(),
        );
    }
}
