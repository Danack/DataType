<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\DateTimeOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class DateTimeOrDefaultNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[DateTimeOrDefault('until', null)]
        public readonly \DateTimeInterface|null $value,
    ) {
    }
}
