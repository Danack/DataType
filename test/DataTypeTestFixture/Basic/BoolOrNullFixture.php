<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\BoolOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class BoolOrNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BoolOrNull('flag')]
        public readonly bool|null $value,
    ) {
    }
}
