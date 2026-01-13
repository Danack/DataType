<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetArrayOfBoolOrNull;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetArrayOfBoolOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetArrayOfBoolOrNull
     */
    public function testWorks()
    {
        $data = [true, false, true];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfBoolOrNull();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());
        $this->assertSame($data, $result->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfBoolOrNull
     */
    public function testMissingGivesNull()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorageAtItems = $dataStorage->moveKey('items');
        $rule = new GetArrayOfBoolOrNull();

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
     * @covers \DataType\ExtractRule\GetArrayOfBoolOrNull
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfBoolOrNull();
        $validator = new ProcessedValues();

        $input = 'banana';

        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::ERROR_MESSAGE_NOT_ARRAY]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfBoolOrNull
     */
    public function testErrorsOnType()
    {
        $data = [true, false, 'banana'];

        $rule = new GetArrayOfBoolOrNull();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/2',
            Messages::ERROR_BOOL_BAD_STRING,
            $validationProblems
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfBoolOrNull
     */
    public function testDescription()
    {
        $rule = new GetArrayOfBoolOrNull();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);
        $this->assertFalse($description->getRequired());
    }
}
