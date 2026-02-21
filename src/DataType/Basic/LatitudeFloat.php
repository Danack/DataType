<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetFloat;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\RangeFloatValue;

/**
 * Required float input for latitude (-90 to 90 inclusive).
 */
#[\Attribute]
class LatitudeFloat implements HasInputType
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
            new GetFloat(),
            new RangeFloatValue(self::MIN_LATITUDE, self::MAX_LATITUDE),
        );
    }
}
