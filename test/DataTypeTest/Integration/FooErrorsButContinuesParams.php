<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\DataType;
use DataType\ExtractRule\GetInt;
use DataType\InputType;
use DataType\ProcessRule\AlwaysErrorsButDoesntHaltRule;
use DataType\ProcessRule\CastToInt;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinIntValue;
use DataType\SafeAccess;

class FooErrorsButContinuesParams implements DataType
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
     * @return array<int, \DataType\InputType>
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
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
