<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\DataType;
use DataType\ExtractRule\GetInt;
use DataType\InputType;
use DataType\ProcessRule\CastToInt;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinIntValue;
use DataType\SafeAccess;

class SingleIntParams implements DataType
{
    use SafeAccess;

    private int $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'limit',
                new GetInt(),
                new CastToInt(),
                new MinIntValue(0),
                new MaxIntValue(100)
            )
        ];
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
