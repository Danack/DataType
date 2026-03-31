<?php

namespace DataTypeTestFixture\Basic;

use DataType\Basic\BasicInteger;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class BasicIntegerFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicInteger('integer_input')]
        public readonly int $value,
    ) {
    }
}
