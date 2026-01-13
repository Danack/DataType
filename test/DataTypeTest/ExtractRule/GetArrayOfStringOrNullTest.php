<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetArrayOfStringOrNull;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetArrayOfStringOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetArrayOfStringOrNull
     */
    public function testWorks()
    {
        $data = ['foo', 'bar', 'quux'];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfStringOrNull();
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
     * @covers \DataType\ExtractRule\GetArrayOfStringOrNull
     */
    public function testMissingGivesNull()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorageAtItems = $dataStorage->moveKey('items');
        $rule = new GetArrayOfStringOrNull();

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
     * @covers \DataType\ExtractRule\GetArrayOfStringOrNull
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfStringOrNull();
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
     * @covers \DataType\ExtractRule\GetArrayOfStringOrNull
     */
    public function testErrorsOnType()
    {
        $data = ['foo', 6, 'bar', 'banana'];

        $rule = new GetArrayOfStringOrNull();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/1',
            \DataType\Messages::STRING_EXPECTED,
            $validationProblems
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfStringOrNull
     */
    public function testDescription()
    {
        $rule = new GetArrayOfStringOrNull();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);
        $this->assertFalse($description->getRequired());
    }
}
