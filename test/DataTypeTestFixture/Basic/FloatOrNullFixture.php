<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\FloatOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class FloatOrNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[FloatOrNull('rate')]
        public readonly float|null $value,
    ) {
    }
}
