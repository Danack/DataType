<?php

declare(strict_types = 1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetArrayOfInt;
use DataType\ProcessedValues;
use DataType\ProcessRule\MaxIntValue;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\ProcessRule\AlwaysErrorsButDoesntHaltRule;

/**
 * @coversNothing
 */
class GetArrayOfIntTest extends BaseTestCase
{
    /**
     * @covers  \DataType\ExtractRule\GetArrayOfInt
     */
    public function testWorks()
    {
        $data = [5, 6, 7];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfInt();
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
     * @covers \DataType\ExtractRule\GetArrayOfInt
     */
    public function testMissingGivesError()
    {
        $rule = new GetArrayOfInt();
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
     * @covers \DataType\ExtractRule\GetArrayOfInt
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfInt();
        $validator = new ProcessedValues();

        $input = 'banana';

        $validationResult = $rule->process(
            $validator,
            $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::ERROR_MESSAGE_NOT_ARRAY]
        );
    }






    /**
     * @covers  \DataType\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnType()
    {
        $data = [5, 6, 7, 'banana'];


        $rule = new GetArrayOfInt();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblem(
            '/3',
            Messages::INT_REQUIRED_FOUND_NON_DIGITS2,
            $validationProblems
        );
    }


    /**
     * @covers  \DataType\ExtractRule\GetArrayOfInt
 */
    public function testErrorsOnTypeTwice()
    {
        $data = [5, 6, 7, 'banana', 'sausage'];

        $rule = new GetArrayOfInt();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(2, $validationProblems);
        $this->assertValidationProblem(
            '/3',
            Messages::INT_REQUIRED_FOUND_NON_DIGITS2,
            $validationProblems
        );
        $this->assertValidationProblem(
            '/4',
            Messages::INT_REQUIRED_FOUND_NON_DIGITS2,
            $validationProblems
        );
    }






    /**
     * @covers  \DataType\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnSubsequentRule()
    {
        $error_string = "Why must you fail me so often";
        $data = [5, 6, 7, 5001, 5002, 5003];

        $rule = new GetArrayOfInt(
            new AlwaysErrorsButDoesntHaltRule($error_string),
            new MaxIntValue(20)
        );

        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        for ($x = 3; $x < 6; $x += 1) {
            $this->assertValidationProblem(
                '/' . $x,
                'Value too large. Max allowed is 20',
                $validationProblems
            );
        }

        for ($x = 3; $x < 6; $x += 1) {
            $this->assertValidationProblem(
                '/' . $x,
                $error_string,
                $validationProblems
            );
        }
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfInt
     */
    public function testDescription()
    {
        $rule = new GetArrayOfInt();
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }
}
