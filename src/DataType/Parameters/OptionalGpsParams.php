<?php

declare(strict_types=1);

namespace DataType\Parameters;

use DataType\Basic\OptionalLatitudeFloat;
use DataType\Basic\OptionalLongitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

/**
 * Optional latitude and longitude. Either both must be set or both must be missing.
 */
class OptionalGpsParams implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalLatitudeFloat('latitude')]
        public readonly float|null $latitude,
        #[OptionalLongitudeFloat('longitude', 'latitude')]
        public readonly float|null $longitude,
    ) {
    }
}
