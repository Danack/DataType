<?php

declare(strict_types=1);

namespace DataTypeTest\OpenApi;

use DataType\ExtractRule\GetInt;
use DataType\InputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinIntValue;
use DataType\SafeAccess;
use DataType\Create\CreateFromVarMap;

class RequiredIntExample
{
    use SafeAccess;
    use CreateFromVarMap;

    const NAME = 'amount';

    const MIN = 10;

    const MAX = 100;

    public static function getInputParameterList()
    {
        return [
            new InputType(
                self::NAME,
                new GetInt(),
                new MinIntValue(self::MIN),
                new MaxIntValue(self::MAX)
            ),
        ];
    }
}
