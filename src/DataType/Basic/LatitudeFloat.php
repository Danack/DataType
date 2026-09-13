<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetFloat;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\GreaterOrEqualParam;
use DataType\ProcessRule\RangeFloatValue;

/**
 * Required float input for latitude (-90 to 90 inclusive).
 * If $greaterOrEqualToParam is set, this value must be greater than or equal to that parameter.
 */
#[\Attribute]
class LatitudeFloat implements HasInputType
{
    private const MIN_LATITUDE = -90.0;
    private const MAX_LATITUDE = 90.0;

    public function __construct(
        private string $name,
        private ?string $greaterOrEqualToParam = null,
    ) {
    }

    public function getInputType(): InputType
    {
        $processRules = [
            new RangeFloatValue(self::MIN_LATITUDE, self::MAX_LATITUDE),
        ];
        if ($this->greaterOrEqualToParam !== null) {
            $processRules[] = new GreaterOrEqualParam($this->greaterOrEqualToParam);
        }

        return new InputType(
            $this->name,
            new GetFloat(),
            ...$processRules,
        );
    }
}
