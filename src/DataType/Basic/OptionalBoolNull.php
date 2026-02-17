<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetOptionalBool;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional boolean that returns null when the parameter is not available.
 * For an optional boolean with a default value, use OptionalBool instead.
 */
#[\Attribute]
class OptionalBoolNull implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetOptionalBool(),
            new SkipIfNull(),
        );
    }
}
