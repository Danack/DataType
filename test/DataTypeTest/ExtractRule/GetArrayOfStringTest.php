<?php

declare(strict_types = 1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetArrayOfString;
use DataType\ProcessedValues;
use DataType\ProcessRule\MaxLength;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\ProcessRule\AlwaysErrorsButDoesntHaltRule;


/**
 * @coversNothing
 */
class GetArrayOfStringTest extends BaseTestCase
{
    /**
     * @covers  \DataType\ExtractRule\GetArrayOfInt
     */
    public function testWorks()
    {
        $data = ['foo', 'bar', 'quux'];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfString();
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
        $rule = new GetArrayOfString();
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
        $rule = new GetArrayOfString();
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
        $data = ['foo', 6, 'bar', 'banana'];

        $rule = new GetArrayOfString();
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
     * @covers  \DataType\ExtractRule\GetArrayOfInt
 */
    public function testErrorsOnTypeTwice()
    {
        $data = ['banana', 'sausage', 'pineapple', 4, 5];

        $rule = new GetArrayOfString();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(2, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/3',
            \DataType\Messages::STRING_EXPECTED,
            $validationProblems
        );
        $this->assertValidationProblemRegexp(
            '/4',
            \DataType\Messages::STRING_EXPECTED,
            $validationProblems
        );
    }






    /**
     * @covers  \DataType\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnSubsequentRule()
    {
        $error_string = "Why must you fail me so often";
        $data = ['foo', 'this string is too long.', 'bar', 'this string is also too long.'];

        $rule = new GetArrayOfString(
            new AlwaysErrorsButDoesntHaltRule($error_string),
            new MaxLength(5)
        );

        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();


        $this->assertValidationProblemRegexp(
            '/1',
            Messages::STRING_TOO_LONG,
            $validationProblems
        );

        $this->assertValidationProblemRegexp(
            '/3',
            Messages::STRING_TOO_LONG,
            $validationProblems
        );

        for ($x = 0; $x < 4; $x += 1) {
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
        $rule = new GetArrayOfString();
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }
}
