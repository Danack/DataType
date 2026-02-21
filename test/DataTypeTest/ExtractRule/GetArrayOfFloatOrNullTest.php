<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetArrayOfFloatOrNull;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetArrayOfFloatOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetArrayOfFloatOrNull
     */
    public function testWorks()
    {
        $data = [5.5, 6.0, 7.0];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfFloatOrNull();
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
     * @covers \DataType\ExtractRule\GetArrayOfFloatOrNull
     */
    public function testMissingGivesNull()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorageAtItems = $dataStorage->moveKey('items');
        $rule = new GetArrayOfFloatOrNull();

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
     * @covers \DataType\ExtractRule\GetArrayOfFloatOrNull
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfFloatOrNull();
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
     * @covers \DataType\ExtractRule\GetArrayOfFloatOrNull
     */
    public function testErrorsOnType()
    {
        $data = [5.0, 6.0, 7.0, 'banana'];

        $rule = new GetArrayOfFloatOrNull();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/3',
            Messages::FLOAT_REQUIRED,
            $validationProblems
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfFloatOrNull
     */
    public function testDescription()
    {
        $rule = new GetArrayOfFloatOrNull();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);
        $this->assertFalse($description->getRequired());
    }
}
