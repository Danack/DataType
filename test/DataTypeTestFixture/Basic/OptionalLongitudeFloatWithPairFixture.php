<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\OptionalLatitudeFloat;
use DataType\Basic\OptionalLongitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class OptionalLongitudeFloatWithPairFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalLatitudeFloat('latitude')]
        public readonly float|null $latitude,
        #[OptionalLongitudeFloat('longitude', 'latitude')]
        public readonly float|null $value,
    ) {
    }
}
