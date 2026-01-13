<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetArrayOfBool;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetArrayOfBoolTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetArrayOfBool
     */
    public function testWorks()
    {
        $data = [true, false, true];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfBool();
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
     * @covers \DataType\ExtractRule\GetArrayOfBool
     */
    public function testWorksWithStrings()
    {
        $data = ['true', 'false', 'true'];
        $expected = [true, false, true];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfBool();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());
        $this->assertSame($expected, $result->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfBool
     */
    public function testMissingGivesError()
    {
        $rule = new GetArrayOfBool();
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
     * @covers \DataType\ExtractRule\GetArrayOfBool
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfBool();
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
     * @covers \DataType\ExtractRule\GetArrayOfBool
     */
    public function testErrorsOnType()
    {
        $data = [true, false, 'banana'];

        $rule = new GetArrayOfBool();
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
     * @covers \DataType\ExtractRule\GetArrayOfBool
     */
    public function testErrorsOnTypeTwice()
    {
        $data = [true, false, 'banana', 'sausage'];

        $rule = new GetArrayOfBool();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(2, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/2',
            Messages::ERROR_BOOL_BAD_STRING,
            $validationProblems
        );
        $this->assertValidationProblemRegexp(
            '/3',
            Messages::ERROR_BOOL_BAD_STRING,
            $validationProblems
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfBool
     */
    public function testDescription()
    {
        $rule = new GetArrayOfBool();
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }
}
