<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\Create\CreateFromVarMap;
use DataType\Create\CreateArrayOfTypeFromArray;
use DataType\ExtractRule\GetInt;
use DataType\ExtractRule\GetString;
use DataType\InputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinLength;
use DataType\SafeAccess;
use DataType\DataType;

class ReviewScore implements DataType
{
    use SafeAccess;
    use CreateFromVarMap;
    use CreateArrayOfTypeFromArray;

    private int $score;

    private string $comment;

    /**
     *
     * @param int $foo
     * @param string $bar
     */
    public function __construct(int $score, string $comment)
    {
        $this->score = $score;
        $this->comment = $comment;
    }

    /**
     * @return \DataType\InputType[]
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'score',
                new GetInt(),
                new MaxIntValue(100)
            ),
            new InputType(
                'comment',
                new GetString(),
                new MinLength(4)
            ),
        ];
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
