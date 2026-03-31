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

class OptionalLongitudeFloatFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalLongitudeFloat('lng')]
        public readonly float|null $value,
    ) {
    }
}
