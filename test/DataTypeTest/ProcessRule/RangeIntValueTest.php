<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\RangeIntValue;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class RangeIntValueTest extends BaseTestCase
{
    public function provideMinIntValueCases()
    {
        $minValue = 100;
        $maxValue = 200;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, $maxValue, (string)$exactValue, false],
            [$minValue, $maxValue, (string)$overValue, false],
//            // TODO - think about these cases.
//            [$minValue, 'banana', true]
        ];
    }

    public function provideMaxIntCases()
    {
        $minValue = 100;
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$minValue, $maxValue, (string)$underValue],
            [$minValue, $maxValue, (string)$exactValue],


            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    public function provideRangeIntValueCases()
    {
        yield from $this->provideMinIntValueCases();
        yield from $this->provideMaxIntCases();
    }

    /**
     * @dataProvider provideRangeIntValueCases
     * @covers \DataType\ProcessRule\RangeIntValue
     */
    public function testValidation(int $minValue, int $maxValue, string $inputValue)
    {
        $rule = new RangeIntValue($minValue, $maxValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }


    public function provideRangeIntErrorCases()
    {
        // Minimum boundary tests
        $minValue = 100;
        $maxValue = 200;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        yield [$minValue, $maxValue, (string)$underValue, Messages::INT_TOO_SMALL];

        // Maximum boundary tests.
        $minValue = 100;
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        yield [$minValue, $maxValue, (string)$overValue, Messages::INT_TOO_LARGE];
    }


    /**
     * @dataProvider provideRangeIntErrorCases
     * @covers \DataType\ProcessRule\RangeIntValue
     */
    public function testErrors($minValue, $maxValue, $inputValue, $message)
    {
        $rule = new RangeIntValue($minValue, $maxValue);
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
     * @covers \DataType\ProcessRule\RangeIntValue
     */
    public function testDescription()
    {
        $rule = new RangeIntValue(10, 20);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(10, $description->getMinimum());
        $this->assertFalse($description->getExclusiveMinimum());

        $this->assertSame(20, $description->getMaximum());
        $this->assertFalse($description->getExclusiveMaximum());
    }
}
