<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetBoolOrNull;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Required parameter that may be null. When the value is present it must be a boolean; when null, the property receives null.
 */
#[\Attribute]
class BoolOrNull implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetBoolOrNull(),
        );
    }
}
