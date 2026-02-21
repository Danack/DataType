<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetOptionalFloat;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\RangeFloatValue;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional float input for latitude (-90 to 90 inclusive). When the parameter is missing, the property receives null.
 */
#[\Attribute]
class OptionalLatitudeFloat implements HasInputType
{
    private const MIN_LATITUDE = -90.0;
    private const MAX_LATITUDE = 90.0;

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
            new RangeFloatValue(self::MIN_LATITUDE, self::MAX_LATITUDE),
        );
    }
}
