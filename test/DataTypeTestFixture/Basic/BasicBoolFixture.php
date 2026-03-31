<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\BasicBool;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class BasicBoolFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicBool('flag')]
        public readonly bool $value,
    ) {
    }
}
