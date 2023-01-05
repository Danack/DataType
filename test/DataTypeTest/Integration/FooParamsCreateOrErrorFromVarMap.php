<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\Create\CreateOrErrorFromVarMap;

class FooParamsCreateOrErrorFromVarMap extends FooParams
{
    use CreateOrErrorFromVarMap;
}
