<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\Create\CreateFromArray;
use DataType\Create\CreateFromJson;
use DataType\Create\CreateFromRequest;
use DataType\Create\CreateOrErrorFromArray;
use DataType\Create\CreateOrErrorFromJson;
use DataType\Create\CreateOrErrorFromRequest;
use DataType\ExtractRule\GetArrayOfInt;
use DataType\InputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinIntValue;
use DataType\SafeAccess;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\MaxLength;
use DataType\ExtractRule\GetString;
use DataType\DataType;

class IntArrayParams implements DataType
{
    use SafeAccess;
    use CreateFromArray;
    use CreateFromJson;
    use CreateFromRequest;
    use CreateOrErrorFromArray;
    use CreateOrErrorFromJson;
    use CreateOrErrorFromRequest;


    /** @var string  */
    private $name;

    /** @var int[] */
    private $counts;

    /**
     *
     * @param string $name
     * @param int[] $values
     */
    public function __construct(string $name, array $counts)
    {
        $this->name = $name;
        $this->counts = $counts;
    }

    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'name',
                new GetString(),
                new MinLength(4),
                new MaxLength(16)
            ),
            new InputType(
                'counts',
                new GetArrayOfInt(
                    new MinIntValue(1),
                    new MaxIntValue(50)
                ),
                new ArrayAllMultiplesOf(3)
            )
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int[]
     */
    public function getCounts(): array
    {
        return $this->counts;
    }
}
