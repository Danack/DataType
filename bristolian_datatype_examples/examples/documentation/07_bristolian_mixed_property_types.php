<?php

declare(strict_types=1);

/**
 * Bristolian-style: library Basic* + project property types on one DataType.
 * Doc extract: Example_bristolian_mixed_property_types
 *
 * Modelled on BristolStairsGpsParams / CreateRoomNoteParam patterns.
 */

namespace BristolianDatatypeExamples\Bristolian;

use DataType\Basic\OptionalLatitudeFloat;
use DataType\Basic\OptionalLongitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use VarMap\VarMap;

// Example_bristolian_mixed_property_types start
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

function stairs_update_position(VarMap $varMap): BristolStairsGpsParams
{
    // VarMap is how Bristolian often passes request variables into create*.
    return BristolStairsGpsParams::createFromVarMap($varMap);
}
// Example_bristolian_mixed_property_types end
