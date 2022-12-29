<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\Create\CreateFromVarMap;
use TypeSpec\Create\CreateArrayOfTypeFromArray;
use TypeSpec\ExtractRule\GetInt;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\DataType;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MinLength;
use TypeSpec\SafeAccess;
use TypeSpec\HasDataTypeList;

class ReviewScore implements HasDataTypeList
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
     * @return \TypeSpec\DataType[]
     */
    public static function getDataTypeList(): array
    {
        return [
            new DataType(
                'score',
                new GetInt(),
                new MaxIntValue(100)
            ),
            new DataType(
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
