<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\ExtractRule\GetInt;
use DataType\InputType;
use DataType\SafeAccess;
use DataType\ExtractRule\GetString;
use DataType\Create\CreateOrErrorFromVarMap;
use DataType\ProcessRule\DuplicatesParam;

class DuplicateButWrongTypeParams
{
    use SafeAccess;
    use CreateOrErrorFromVarMap;

    private int $days;

    private string $days_repeat;

    public function __construct(int $days, string $days_repeat)
    {
        $this->days = $days;
        $this->days_repeat = $days_repeat;
    }

    public static function getInputParameterList()
    {
        return [
            new InputType(
                'days',
                new GetInt()
            ),
            new InputType(
                'days_repeat',
                new GetString(),
                new DuplicatesParam('days')
            ),
        ];
    }

    /**
     * @return int
     */
    public function getDays(): int
    {
        return $this->days;
    }

    /**
     * @return string
     */
    public function getDaysRepeat(): string
    {
        return $this->days_repeat;
    }
}
