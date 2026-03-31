<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\StringOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class StringOrNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[StringOrNull('name')]
        public readonly string|null $value,
    ) {
    }
}
