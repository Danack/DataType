<?php

declare(strict_types=1);

namespace DataType\Parameters;

use DataType\Basic\LatitudeFloat;
use DataType\Basic\LongitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

/**
 * Geographic bounding box. max_* must be greater than or equal to the matching min_*.
 * Does not support boxes that cross the antimeridian.
 */
class BoundingBox implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[LatitudeFloat('min_latitude')]
        public readonly float $min_latitude,
        #[LatitudeFloat('max_latitude', 'min_latitude')]
        public readonly float $max_latitude,
        #[LongitudeFloat('min_longitude')]
        public readonly float $min_longitude,
        #[LongitudeFloat('max_longitude', 'min_longitude')]
        public readonly float $max_longitude,
    ) {
    }
}
