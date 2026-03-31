<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\OptionalInteger;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class OptionalIntegerFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalInteger('count')]
        public readonly int|null $value,
    ) {
    }
}
