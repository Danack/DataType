<?php

namespace DataTypeTestFixture\Basic;

use DataType\Basic\BasicSha;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class BasicShaFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicSha('sha_input')]
        public readonly string $value,
    ) {
    }
}
