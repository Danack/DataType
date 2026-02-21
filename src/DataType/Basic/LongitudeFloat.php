<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetFloat;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\RangeFloatValue;

/**
 * Required float input for longitude (-180 to 180 inclusive).
 */
#[\Attribute]
class LongitudeFloat implements HasInputType
{
    private const MIN_LONGITUDE = -180.0;
    private const MAX_LONGITUDE = 180.0;

    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetFloat(),
            new RangeFloatValue(self::MIN_LONGITUDE, self::MAX_LONGITUDE),
        );
    }
}
