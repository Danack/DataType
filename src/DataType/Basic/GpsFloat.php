<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetFloat;
use DataType\ExtractRule\GetOptionalFloat;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional float input for latitude/longitude (-90 to 90, -180 to 180). When the parameter is missing, the property receives null.
 */
#[\Attribute]
class GpsFloat implements HasInputType
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
            // TODO - add sanity checks?
        );
    }
}
