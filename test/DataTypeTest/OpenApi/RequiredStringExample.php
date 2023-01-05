<?php

declare(strict_types=1);

namespace DataTypeTest\OpenApi;

use DataType\InputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinLength;
use DataType\ExtractRule\GetString;
use DataType\SafeAccess;
use DataType\Create\CreateFromVarMap;

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
