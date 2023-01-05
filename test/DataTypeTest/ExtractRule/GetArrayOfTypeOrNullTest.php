<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\ExtractRule\GetArrayOfTypeOrNull;
use DataTypeTest\BaseTestCase;
use DataTypeTest\Integration\ReviewScore;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetArrayOfTypeOrNullTest extends BaseTestCase
{

    /**
     * @covers \DataType\ExtractRule\GetArrayOfTypeOrNull
     */
    public function testWorks()
    {
        $data = [
            ['score' => 5, 'comment' => 'Hello world']
        ];

        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);
        $processedValues = new ProcessedValues();
        $result = $rule->process(
            $processedValues,
            TestArrayDataStorage::fromArray($data)
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfTypeOrNull
     */
    public function testWorksWhenNotSet()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorageAtItems = $dataStorage->moveKey('items');
        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);

        $processedValues = new ProcessedValues();
        $result = $rule->process(
            $processedValues,
            $dataStorageAtItems
        );

        $this->assertNull($result->getValue());
        $this->assertEmpty($result->getValidationProblems());
        $this->assertNoProblems($result);
    }


    /**
     * @covers \DataType\ExtractRule\GetArrayOfTypeOrNull
     */
    public function testDescription()
    {
        $rule = new GetArrayOfTypeOrNull(ReviewScore::class);
        $description = $this->applyRuleToDescription($rule);
        $this->assertFalse($description->getRequired());
    }
}
