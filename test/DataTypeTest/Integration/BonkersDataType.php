<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\ExtractRule\GetDatetime;
use DataType\InputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\SafeAccess;

/**
 * This is a bonkers datatype that has an error in getInputTypes.
 * A DateTime object is extracted, which then is processed with an
 * MaxIntValue process rule, which is nonsensical.
 */
class BonkersDataType implements DataType
{
    use SafeAccess;
    use CreateFromVarMap;

    public function __construct(public readonly int $bad_type)
    {
    }

    /**
     * @return array<int, \DataType\InputType>
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'bad_type',
                new GetDatetime(),
                new MaxIntValue(100)
            )
        ];
    }
}
