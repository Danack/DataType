<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\PositiveInt;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class PositiveIntTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['5', 5, false],
            ['0', 0, false], // close enough
            [PositiveInt::MAX_SANE_VALUE, PositiveInt::MAX_SANE_VALUE, false],
            [PositiveInt::MAX_SANE_VALUE - 1, PositiveInt::MAX_SANE_VALUE - 1, false],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \DataType\ProcessRule\PositiveInt
     */
    public function testValidation($testValue, $expectedResult, $expectError)
    {
        $rule = new PositiveInt();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedResult);
    }


    public function provideTestErrors()
    {
        return [
            ['5.5', Messages::INT_REQUIRED_FOUND_NON_DIGITS], // not an int
            ['banana', Messages::INT_REQUIRED_FOUND_NON_DIGITS], // not an int
            [PositiveInt::MAX_SANE_VALUE + 1 , Messages::INT_OVER_LIMIT],
        ];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\PositiveInt
     */
    public function testErrors($testValue, $message)
    {
        $rule = new PositiveInt();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ProcessRule\PositiveInt
     */
    public function testDescription()
    {
        $rule = new PositiveInt();
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(0, $description->getMinimum());
        $this->assertFalse($description->getExclusiveMinimum());
    }
}
