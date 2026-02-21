<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetArrayOfIntOrNull;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetArrayOfIntOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetArrayOfIntOrNull
     */
    public function testWorks()
    {
        $data = [5, 6, 7];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfIntOrNull();
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
     * @covers \DataType\ExtractRule\GetArrayOfIntOrNull
     */
    public function testMissingGivesNull()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorageAtItems = $dataStorage->moveKey('items');
        $rule = new GetArrayOfIntOrNull();

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
     * @covers \DataType\ExtractRule\GetArrayOfIntOrNull
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfIntOrNull();
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
     * @covers \DataType\ExtractRule\GetArrayOfIntOrNull
     */
    public function testErrorsOnType()
    {
        $data = [5, 6, 7, 'banana'];

        $rule = new GetArrayOfIntOrNull();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/3',
            Messages::INT_REQUIRED_FOUND_NON_DIGITS2,
            $validationProblems
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfIntOrNull
     */
    public function testDescription()
    {
        $rule = new GetArrayOfIntOrNull();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);
        $this->assertFalse($description->getRequired());
    }
}
