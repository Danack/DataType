<?php

namespace DataTypeTestFixture\Basic;

use DataType\Basic\BasicDateTime;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class BasicDateTimeFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicDateTime('datetime_input')]
        public readonly \DateTimeInterface $value,
    ) {
    }
}
