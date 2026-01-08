<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetOptionalFloat;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class GetOptionalFloatTest extends BaseTestCase
{
    public function provideTestCases()
    {
        // Test value is read as string
        yield ['5', 5];

        // Testread as int
        yield [5, 5];

        yield ['5', 5];
        yield ['555555', 555555];
        yield ['1000.1', 1000.1];
        yield ['-1000.1', -1000.1];

        // Test missing param is null
        // TODO test missing as separate case.
//        yield [[], null];
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalFloat
     * @dataProvider provideTestCases
     * @param int|string $input
     * @param float|int $expectedValue
     */
    public function testValidation($input, $expectedValue)
    {
        $rule = new GetOptionalFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        // If set, null not allowed
        yield [null, Messages::FLOAT_REQUIRED_WRONG_TYPE];

        // if set, empty string not allowed
        yield ['', Messages::NEED_FLOAT_NOT_EMPTY_STRING];
        yield ['6 apples', Messages::FLOAT_REQUIRED_FOUND_WHITESPACE];
        yield ['banana', Messages::FLOAT_REQUIRED];
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalFloat
     * @dataProvider provideTestErrorCases
     * @param mixed $inputValue
     * @param string $message
     */
    public function testErrors($inputValue, $message)
    {
        $validator = new ProcessedValues();
        $rule = new GetOptionalFloat();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );

        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalFloat
     */
    public function testMissingGivesNull()
    {
        $rule = new GetOptionalFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertNull($validationResult->getValue());
    }



    /**
     * @covers \DataType\ExtractRule\GetOptionalFloat
     */
    public function testDescription()
    {
        $rule = new GetOptionalFloat();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('number', $description->getType());
        $this->assertFalse($description->getRequired());
    }
}
