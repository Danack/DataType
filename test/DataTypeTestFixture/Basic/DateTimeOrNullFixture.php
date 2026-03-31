<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\DateTimeOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class DateTimeOrNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[DateTimeOrNull('when')]
        public readonly \DateTimeInterface|null $value,
    ) {
    }
}
