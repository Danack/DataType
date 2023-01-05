<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\Create\CreateFromVarMap;

class FooParamsCreateFromVarMap extends FooParams
{
    use CreateFromVarMap;
}
