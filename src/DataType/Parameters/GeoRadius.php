<?php

declare(strict_types=1);

namespace DataType\Parameters;

use DataType\Basic\LatitudeFloat;
use DataType\Basic\LongitudeFloat;
use DataType\Basic\RadiusMetresFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

/**
 * Geographic point with a search radius in metres.
 */
class GeoRadius implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[LatitudeFloat('latitude')]
        public readonly float $latitude,
        #[LongitudeFloat('longitude')]
        public readonly float $longitude,
        #[RadiusMetresFloat('radius_metres')]
        public readonly float $radius_metres,
    ) {
    }
}
