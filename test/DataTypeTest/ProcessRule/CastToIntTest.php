<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\CastToInt;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class CastToIntTest extends BaseTestCase
{
    public static function provideIntValueWorksCases()
    {
        return [
            [5, 5],
            ['5', 5],
            ['-10', -10],
            ['555555', 555555],
            [(string)CastToInt::MAX_SANE_VALUE, CastToInt::MAX_SANE_VALUE]
        ];
    }

    /**
     * @covers \DataType\ProcessRule\CastToInt
     */
    #[DataProvider('provideIntValueWorksCases')]
    public function testValidationWorks(int|string $inputValue, int $expectedValue)
    {
        $rule = new CastToInt();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public static function providesDetectsErrorsCorrectly()
    {
        return [
            // todo - we should test the exact error.
            [[], Messages::INT_REQUIRED_UNSUPPORTED_TYPE],
            ['5.0', Messages::INT_REQUIRED_FOUND_NON_DIGITS2],
            ['5.5', Messages::INT_REQUIRED_FOUND_NON_DIGITS2],
            ['banana', Messages::INT_REQUIRED_FOUND_NON_DIGITS2],
            ['', Messages::INT_REQUIRED_FOUND_EMPTY_STRING],
            [(string)(CastToInt::MAX_SANE_VALUE + 1), Messages::INTEGER_TOO_LONG]
        ];
    }

    /**
     * @covers \DataType\ProcessRule\CastToInt
     * @param array<int, mixed>|string $inputValue
     */
    #[DataProvider('providesDetectsErrorsCorrectly')]
    public function testDetectsErrorsCorrectly($inputValue, string $message)
    {
        $rule = new CastToInt();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ProcessRule\CastToInt
     */
    public function testDescription()
    {
        $rule = new CastToInt();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('integer', $description->getType());
    }
}
