<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetOptionalFloat;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional float input. When the parameter is missing, the property receives null.
 */
#[\Attribute]
class OptionalFloat implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetOptionalFloat(),
            new SkipIfNull(),
        );
    }
}
