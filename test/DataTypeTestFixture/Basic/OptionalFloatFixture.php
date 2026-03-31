<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\OptionalFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class OptionalFloatFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalFloat('rate')]
        public readonly float|null $value,
    ) {
    }
}
