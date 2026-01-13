<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetArrayOfDatetimeOrNull;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetArrayOfDatetimeOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetArrayOfDatetimeOrNull
     */
    public function testWorks()
    {
        $data = [
            '2002-10-02T10:00:00-05:00',
            '2003-11-03T11:00:00-05:00',
            '2004-12-04T12:00:00-05:00'
        ];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfDatetimeOrNull();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());
        $this->assertCount(3, $result->getValue());
        $this->assertInstanceOf(\DateTimeInterface::class, $result->getValue()[0]);
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfDatetimeOrNull
     */
    public function testMissingGivesNull()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorageAtItems = $dataStorage->moveKey('items');
        $rule = new GetArrayOfDatetimeOrNull();

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
     * @covers \DataType\ExtractRule\GetArrayOfDatetimeOrNull
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfDatetimeOrNull();
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
     * @covers \DataType\ExtractRule\GetArrayOfDatetimeOrNull
     */
    public function testErrorsOnType()
    {
        $data = [
            '2002-10-02T10:00:00-05:00',
            'invalid datetime string'
        ];

        $rule = new GetArrayOfDatetimeOrNull();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/1',
            Messages::ERROR_INVALID_DATETIME,
            $validationProblems
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfDatetimeOrNull
     */
    public function testDescription()
    {
        $rule = new GetArrayOfDatetimeOrNull();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);
        $this->assertFalse($description->getRequired());
    }
}
