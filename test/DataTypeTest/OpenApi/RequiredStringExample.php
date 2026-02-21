<?php

declare(strict_types=1);

namespace DataTypeTest\OpenApi;

use DataType\Create\CreateFromVarMap;
use DataType\ExtractRule\GetString;
use DataType\InputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinLength;
use DataType\SafeAccess;

class RequiredStringExample
{
    use SafeAccess;
    use CreateFromVarMap;

    const NAME = 'status';

    const MIN_LENGTH = 10;

    const MAX_LENGTH = 100;

    public static function getInputParameterList()
    {
        return [
            new InputType(
                self::NAME,
                new GetString(),
                new MaxLength(self::MAX_LENGTH),
                new MinLength(self::MIN_LENGTH)
            ),
        ];
    }
}
