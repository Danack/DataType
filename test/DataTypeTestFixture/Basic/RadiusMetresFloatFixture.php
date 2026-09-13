<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\RadiusMetresFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

class RadiusMetresFloatFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[RadiusMetresFloat('radius_metres')]
        public readonly float $value,
    ) {
    }
}
