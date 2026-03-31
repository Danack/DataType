<?php

declare(strict_types=1);

namespace DataTypeTestFixture\Integration;

use DataType\Create\CreateFromVarMap;
use DataTypeTestFixture\Integration\FooParams;

class FooParamsCreateFromVarMap extends FooParams
{
    use CreateFromVarMap;
}
