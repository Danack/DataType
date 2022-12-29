<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\DataType;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MinIntValue;
use TypeSpec\SafeAccess;
use TypeSpec\HasDataTypeList;
use TypeSpec\ProcessRule\CastToInt;

class FooParams implements HasDataTypeList
{
    use SafeAccess;

    /** @var int  */
    private $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return array
     */
    public static function getDataTypeList(): array
    {
        return [
            new DataType(
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
