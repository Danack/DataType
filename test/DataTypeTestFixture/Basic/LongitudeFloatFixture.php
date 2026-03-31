<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\LongitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class LongitudeFloatFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[LongitudeFloat('lng')]
        public readonly float $value,
    ) {
    }
}
