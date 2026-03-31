<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Integration;

use DataType\Create\CreateOrErrorFromVarMap;
use DataTypeTestFixture\Integration\FooParams;

class FooParamsCreateOrErrorFromVarMap extends FooParams
{
    use CreateOrErrorFromVarMap;
}
