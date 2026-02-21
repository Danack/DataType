<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\RangeFloatValue;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class RangeFloatValueTest extends BaseTestCase
{
    public function provideMinFloatValueCases()
    {
        $minValue = 100;
        $maxValue = 200;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, $maxValue, $exactValue, false],
            [$minValue, $maxValue, $overValue, false],
//            // TODO - think about these cases.
//            [$minValue, 'banana', true]
        ];
    }

    public function provideMaxFloatCases()
    {
        $minValue = 100;
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$minValue, $maxValue, $underValue],
            [$minValue, $maxValue, $exactValue],


            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    public function provideRangeFloatValueCases()
    {
        yield from $this->provideMinFloatValueCases();
        yield from $this->provideMaxFloatCases();
    }

    /**
     * @dataProvider provideRangeFloatValueCases
     * @covers \DataType\ProcessRule\RangeFloatValue
     */
    public function testValidation(float $minValue, float $maxValue, float $inputValue)
    {
        $rule = new RangeFloatValue($minValue, $maxValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }


    public function provideRangeFloatErrorCases()
    {
        // Minimum boundary tests
        $minValue = 100;
        $maxValue = 200;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        yield [$minValue, $maxValue, $underValue, Messages::INT_TOO_SMALL];

        // Maximum boundary tests.
        $minValue = 100;
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        yield [$minValue, $maxValue, $overValue, Messages::INT_TOO_LARGE];
    }


    /**
     * @dataProvider provideRangeFloatErrorCases
     * @covers \DataType\ProcessRule\RangeFloatValue
     */
    public function testErrors(int|float $minValue, int|float $maxValue, float $inputValue, string $message)
    {
        $rule = new RangeFloatValue($minValue, $maxValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $inputValue);
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }



    /**
     * @covers \DataType\ProcessRule\RangeFloatValue
     */
    public function testDescription()
    {
        $rule = new RangeFloatValue(10.0, 20.0);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(10.0, $description->getMinimum());
        $this->assertFalse($description->getExclusiveMinimum());

        $this->assertSame(20.0, $description->getMaximum());
        $this->assertFalse($description->getExclusiveMaximum());
    }
}
