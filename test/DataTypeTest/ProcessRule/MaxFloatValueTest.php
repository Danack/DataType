<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\MaxFloatValue;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class MaxFloatValueTest extends BaseTestCase
{
    public static function provideMaxFloatCases()
    {
        $maxValue = 256.5;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, $underValue],
            [$maxValue, $exactValue],
            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MaxFloatValue
     */
    #[DataProvider('provideMaxFloatCases')]
    public function testValidation(float $maxValue, float $inputValue)
    {
        $rule = new MaxFloatValue($maxValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataStorage
        );


        $this->assertNoProblems($validationResult);
    }


    public static function provideMaxFloatErrors()
    {
        $maxValue = 256.0;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, $overValue],

            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MaxFloatValue
     */
    #[DataProvider('provideMaxFloatErrors')]
    public function testErrors(float $maxValue, float $inputValue)
    {
        $rule = new MaxFloatValue($maxValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $inputValue);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::INT_TOO_LARGE,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString($validationResult, (string)$maxValue);
    }

    /**
     * @covers \DataType\ProcessRule\MaxFloatValue
     */
    public function testDescription()
    {

        $maxValue = 20.0;
        $rule = new MaxFloatValue($maxValue);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame($maxValue, $description->getMaximum());
        $this->assertFalse($description->isExclusiveMaximum());
    }
}
