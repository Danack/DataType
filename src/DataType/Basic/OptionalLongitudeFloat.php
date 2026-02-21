<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetOptionalFloat;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\BothOrNeitherParam;
use DataType\ProcessRule\RangeFloatValue;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional float input for longitude (-180 to 180 inclusive). When the parameter is missing, the property receives null.
 * If $pairWithParam is not null, this parameter and that one must either both be set or both be missing.
 */
#[\Attribute]
class OptionalLongitudeFloat implements HasInputType
{
    private const MIN_LONGITUDE = -180.0;
    private const MAX_LONGITUDE = 180.0;

    public function __construct(
        private string $name,
        private ?string $pairWithParam = null,
    ) {
    }

    public function getInputType(): InputType
    {
        $processRules = [];
        if ($this->pairWithParam !== null) {
            $processRules[] = new BothOrNeitherParam($this->pairWithParam);
        }
        $processRules[] = new SkipIfNull();
        $processRules[] = new RangeFloatValue(self::MIN_LONGITUDE, self::MAX_LONGITUDE);

        return new InputType(
            $this->name,
            new GetOptionalFloat(),
            ...$processRules,
        );
    }
}
