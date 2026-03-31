<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\FloatOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class FloatOrDefaultNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[FloatOrDefault('rate', null)]
        public readonly float|null $value,
    ) {
    }
}
