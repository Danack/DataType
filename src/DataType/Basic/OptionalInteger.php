<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetOptionalInt;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional integer input. When the parameter is missing, the property receives null.
 */
#[\Attribute]
class OptionalInteger implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetOptionalInt(),
            new SkipIfNull(),
        );
    }
}
