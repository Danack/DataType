<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\StringOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class StringOrDefaultFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[StringOrDefault('sort', 'date')]
        public readonly string|null $value,
    ) {
    }
}
