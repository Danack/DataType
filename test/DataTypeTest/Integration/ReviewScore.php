<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\Create\CreateArrayOfTypeFromArray;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\ExtractRule\GetInt;
use DataType\ExtractRule\GetString;
use DataType\InputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinLength;
use DataType\SafeAccess;

class ReviewScore implements DataType
{
    use SafeAccess;
    use CreateFromVarMap;
    use CreateArrayOfTypeFromArray;

    private int $score;

    private string $comment;

    /**
     *
     * @param int $score
     * @param string $comment
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
