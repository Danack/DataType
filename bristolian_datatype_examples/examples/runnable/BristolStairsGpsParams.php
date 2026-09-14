<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Runnable;

use DataType\Basic\OptionalLatitudeFloat;
use DataType\Basic\OptionalLongitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

class BristolStairsGpsParams implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalLatitudeFloat('gps_latitude')]
        public readonly float|null $latitude,
        #[OptionalLongitudeFloat('gps_longitude', 'gps_latitude')]
        public readonly float|null $longitude,
    ) {
    }
}
