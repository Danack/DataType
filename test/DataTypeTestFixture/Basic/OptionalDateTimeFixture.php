<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Basic;

use DataType\Basic\OptionalDateTime;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class OptionalDateTimeFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalDateTime('when')]
        public readonly \DateTimeInterface|null $value,
    ) {
    }
}
