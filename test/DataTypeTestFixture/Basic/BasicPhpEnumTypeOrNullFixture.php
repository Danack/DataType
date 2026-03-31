<?php

namespace DataTypeTestFixture\Basic;

use DataType\Basic\BasicPhpEnumTypeOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\TestEnum;

class BasicPhpEnumTypeOrNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicPhpEnumTypeOrNull('enum_input', TestEnum::class)]
        public readonly string|null $value,
    ) {
    }
}

/**
 * Used for testing. Update the tests if you change the number of entries.
 */
