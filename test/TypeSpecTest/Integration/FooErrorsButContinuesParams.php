<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\DataType;
use TypeSpec\ExtractRule\GetInt;
use TypeSpec\HasDataTypeList;
use TypeSpec\ProcessRule\AlwaysErrorsButDoesntHaltRule;
use TypeSpec\ProcessRule\CastToInt;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MinIntValue;
use TypeSpec\SafeAccess;

class FooErrorsButContinuesParams implements HasDataTypeList
{
    use SafeAccess;

    public const MESSAGE = "Why must you always fail me?";

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
                new AlwaysErrorsButDoesntHaltRule(self::MESSAGE),
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
