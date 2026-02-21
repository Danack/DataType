<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\ExtractRule\GetOptionalFloat;
use DataType\InputType;
use DataType\ProcessRule\BothOrNeitherParam;
use DataType\ProcessRule\RangeFloatValue;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional latitude and longitude. Either both must be set or both must be missing.
 */
class OptionalGpsParams implements DataType
{
    use CreateFromVarMap;

    public function __construct(
        public readonly float|null $latitude,
        public readonly float|null $longitude,
    ) {
    }

    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'latitude',
                new GetOptionalFloat(),
                new SkipIfNull(),
                new RangeFloatValue(-90.0, 90.0),
            ),
            new InputType(
                'longitude',
                new GetOptionalFloat(),
                new BothOrNeitherParam('latitude'),
                new SkipIfNull(),
                new RangeFloatValue(-180.0, 180.0),
            ),
        ];
    }
}
