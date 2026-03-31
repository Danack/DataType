<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\FloatOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class FloatOrDefaultFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[FloatOrDefault('rate', 1.0)]
        public readonly float|null $value,
    ) {
    }
}
