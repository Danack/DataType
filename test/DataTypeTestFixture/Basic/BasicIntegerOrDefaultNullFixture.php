<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class BasicIntegerOrDefaultNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicIntegerOrDefault('page', null)]
        public readonly int|null $value,
    ) {
    }
}
