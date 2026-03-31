<?php

namespace DataTypeTestFixture\Basic;

use DataType\Basic\OptionalBasicString;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class OptionalBasicStringFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalBasicString('string_input')]
        public readonly string|null $value,
    ) {
    }
}
