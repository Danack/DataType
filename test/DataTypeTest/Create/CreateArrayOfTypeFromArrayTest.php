<?php

declare(strict_types = 1);

namespace DataTypeTest\Create;

use DataType\Exception\ValidationExceptionData;
use DataTypeTest\BaseTestCase;
use DataTypeTest\Integration\ReviewScore;

/**
 * @coversNothing
 */
class CreateArrayOfTypeFromArrayTest extends BaseTestCase
{
    /**
     * @covers \DataType\Create\CreateArrayOfTypeFromArray
     */
    public function testWorks()
    {
        $scores = [5, 6];
        $comments = [
            'Hello world',
            'Hello world2'
        ];

        $data = [
            ['score' => $scores[0], 'comment' => $comments[0]],
            ['score' => $scores[1], 'comment' => $comments[1]]
        ];

        $items = ReviewScore::createArrayOfTypeFromArray($data);

        $index = 0;
        foreach ($items as $item) {
            $this->assertInstanceOf(ReviewScore::class, $item);
            $this->assertSame($scores[$index], $item->getScore());
            $this->assertSame($comments[$index], $item->getComment());
            $index += 1;
        }
    }

    /**
     * @covers \DataType\Create\CreateArrayOfTypeFromArray
     */
    public function testErrors()
    {
        $scores = [5, 6];
        $comments = [
            'Hello world',
            'Hello world2'
        ];

        $data = [
            ['score' => $scores[0], 'comment' => $comments[0]],
            ['score' => $scores[1], ]
        ];

        $this->expectException(ValidationExceptionData::class);
        $this->expectExceptionMessage("Validation problems /1/comment Value not set.");

        ReviewScore::createArrayOfTypeFromArray($data);
    }
}
