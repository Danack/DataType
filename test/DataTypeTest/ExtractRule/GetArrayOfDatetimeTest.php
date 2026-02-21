<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetArrayOfDatetime;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetArrayOfDatetimeTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetArrayOfDatetime
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

        $rule = new GetArrayOfDatetime();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());
        /** @var array<int, \DateTimeInterface> $value */
        $value = $result->getValue();
        $this->assertCount(3, $value);
        $this->assertInstanceOf(\DateTimeInterface::class, $value[0]);
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfDatetime
     */
    public function testMissingGivesError()
    {
        $rule = new GetArrayOfDatetime();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::ERROR_MESSAGE_NOT_SET]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfDatetime
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfDatetime();
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
     * @covers \DataType\ExtractRule\GetArrayOfDatetime
     */
    public function testErrorsOnType()
    {
        $data = [
            '2002-10-02T10:00:00-05:00',
            'invalid datetime string'
        ];

        $rule = new GetArrayOfDatetime();
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
     * @covers \DataType\ExtractRule\GetArrayOfDatetime
     */
    public function testDescription()
    {
        $rule = new GetArrayOfDatetime();
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }
}
