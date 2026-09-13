<?php

declare(strict_types=1);

namespace DataType\Parameters;

use DataType\Basic\LatitudeFloat;
use DataType\Basic\LongitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

/**
 * Required latitude and longitude.
 */
class GpsParams implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[LatitudeFloat('latitude')]
        public readonly float $latitude,
        #[LongitudeFloat('longitude')]
        public readonly float $longitude,
    ) {
    }
}
