<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetArrayOfType;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;
use DataTypeTest\Integration\ReviewScore;
use DataTypeTest\Integration\SingleIntParams;

/**
 * @coversNothing
 */
class GetArrayOfTypeTest extends BaseTestCase
{

    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testWorksForComplexType()
    {
        $niceComment = "This is great.";
        $badComment = "Not so good.";

        $data = [
            ['score' => 5, 'comment' => $niceComment],
            ['score' => 2, 'comment' => $badComment],
        ];

        $rule = new GetArrayOfType(ReviewScore::class);
        $processedValues = new ProcessedValues();
        $result = $rule->process(
            $processedValues,
            TestArrayDataStorage::fromArray($data)
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());

        /** @var array<int, ReviewScore> $value */
        $value = $result->getValue();
        $this->assertCount(2, $value);

        $item1 = $value[0];
        $this->assertInstanceOf(ReviewScore::class, $item1);
        /** @var ReviewScore $item1 */
        $this->assertSame(5, $item1->getScore());
        $this->assertSame($niceComment, $item1->getComment());


        $item2 = $value[1];
        $this->assertInstanceOf(ReviewScore::class, $item2);
        /** @var ReviewScore $item2 */
        $this->assertSame(2, $item2->getScore());
        $this->assertSame($badComment, $item2->getComment());
    }


    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testWorksForComplexTypeForKey()
    {
        $niceComment = "This is great.";
        $badComment = "Not so good.";

        $data = [
            'items' => [
                ['score' => 5, 'comment' => $niceComment],
                ['score' => 2, 'comment' => $badComment],
            ]
        ];

        $dataStorage = TestArrayDataStorage::fromArray($data);
        $dataStorage = $dataStorage->moveKey('items');

        $rule = new GetArrayOfType(ReviewScore::class);
        $processedValues = new ProcessedValues();
        $result = $rule->process(
            $processedValues,
            $dataStorage
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());

        /** @var array<int, ReviewScore> $value */
        $value = $result->getValue();
        $this->assertCount(2, $value);

        $item1 = $value[0];
        $this->assertInstanceOf(ReviewScore::class, $item1);
        /** @var ReviewScore $item1 */
        $this->assertSame(5, $item1->getScore());
        $this->assertSame($niceComment, $item1->getComment());


        $item2 = $value[1];
        $this->assertInstanceOf(ReviewScore::class, $item2);
        /** @var ReviewScore $item2 */
        $this->assertSame(2, $item2->getScore());
        $this->assertSame($badComment, $item2->getComment());
    }


    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testWorksForIntType()
    {
        $data = [
            ['limit' => 5]
        ];

        $rule = new GetArrayOfType(SingleIntParams::class);
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertFalse($result->isFinalResult());

        /** @var array<int, SingleIntParams> $value */
        $value = $result->getValue();
        $this->assertCount(1, $value);
        $item = $value[0];
        $this->assertInstanceOf(SingleIntParams::class, $item);

        $this->assertNoProblems($result);
        /** @var SingleIntParams $item */
        $this->assertSame(5, $item->getLimit());
    }


    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testEmptyInputProducesEmptyOutput()
    {
        $data = [];

        $rule = new GetArrayOfType(ReviewScore::class);
        $validator = new ProcessedValues();

        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArraySetFirstValue($data)
        );


        $problems = $result->getValidationProblems();
        $this->assertNoValidationProblems($problems);

        $arrayOfType = $result->getValue();
        $this->assertIsArray($arrayOfType);
        $this->assertEmpty($arrayOfType);
    }


    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testScalarInsteadOfArrayErrors()
    {

        $data = [
            'items' => 'a banana'
        ];

        $rule = new GetArrayOfType(ReviewScore::class);
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArraySetFirstValue($data)
        );
        $this->assertTrue($result->isFinalResult());

        $problems = $result->getValidationProblems();

        $this->assertCount(1, $problems);
        $this->assertValidationProblem(
            '/items',
            "Value must be an array.",
            $problems
        );

        $this->assertNull($result->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testScalarInsteadOfEntryArrayErrors()
    {
        $data = [
            // wrong - should be ['limit' => 5]
            5
        ];

        $rule = new GetArrayOfType(SingleIntParams::class);

        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArraySetFirstValue($data)
        );
        $this->assertTrue($result->isFinalResult());
        $validationProblems = $result->getValidationProblems();
        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/0',
            Messages::ERROR_MESSAGE_NOT_ARRAY,
            $validationProblems
        );

        $this->assertNull($result->getValue());
    }


    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testSingleError()
    {
        $data = [
            'items' => [
                ['score' => 5, 'comment' => 'no']
            ],
        ];

        $rule = new GetArrayOfType(ReviewScore::class);
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArraySetFirstValue($data)
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());

        $this->assertCount(1, $result->getValidationProblems());

        $this->assertValidationProblemRegexp(
            '/items/0/comment',
            Messages::STRING_TOO_SHORT,
            $result->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testMultipleErrors()
    {
        $data = [
            ['score' => 5, 'comment' => 'foo'],
            ['score' => 101, 'comment' => 'world']
        ];

        $validator = new ProcessedValues();
        $rule = new GetArrayOfType(ReviewScore::class);
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());

        $validationProblems = $result->getValidationProblems();
        $this->assertCount(2, $validationProblems);

        $this->assertValidationProblemRegexp(
            '/0/comment',
            Messages::STRING_TOO_SHORT,
            $validationProblems
        );

        $this->assertValidationProblem(
            '/1/score',
            "Value too large. Max allowed is 100",
            $validationProblems
        );
    }
    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testWorksForEmptyData()
    {

        $data = [];

        $rule = new GetArrayOfType(ReviewScore::class);
        $processedValues = new ProcessedValues();
        $result = $rule->process(
            $processedValues,
            TestArrayDataStorage::fromArray($data)
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());
        $this->assertIsArray($result->getValue());
        $this->assertEmpty($result->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testDescription()
    {
        $rule = new GetArrayOfType(ReviewScore::class);
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }
}
