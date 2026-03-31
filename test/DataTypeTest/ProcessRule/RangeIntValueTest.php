<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\RangeIntValue;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class RangeIntValueTest extends BaseTestCase
{
    public static function provideMinIntValueCases()
    {
        $minValue = 100;
        $maxValue = 200;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, $maxValue, $exactValue],
            [$minValue, $maxValue, $overValue],
//            // TODO - think about these cases.
//            [$minValue, 'banana', true]
        ];
    }

    public static function provideMaxIntCases()
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

    public static function provideRangeIntValueCases()
    {
        yield from self::provideMinIntValueCases();
        yield from self::provideMaxIntCases();
    }

    /**
     * @covers \DataType\ProcessRule\RangeIntValue
     */
    #[DataProvider('provideRangeIntValueCases')]
    public function testValidation(int $minValue, int $maxValue, int $inputValue)
    {
        $rule = new RangeIntValue($minValue, $maxValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }


    public static function provideRangeIntErrorCases()
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
     * @covers \DataType\ProcessRule\RangeIntValue
     */
    #[DataProvider('provideRangeIntErrorCases')]
    public function testErrors(int $minValue, int $maxValue, int $inputValue, string $message)
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
