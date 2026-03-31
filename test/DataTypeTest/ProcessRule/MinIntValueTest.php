<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\MinIntValue;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class MinIntValueTest extends BaseTestCase
{
    public static function provideMinIntValueCases()
    {
        $minValue = 100;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
//            [$minValue, (string)$underValue, true],
            [$minValue, $exactValue],
            [$minValue, $overValue],

            // TODO - think about these cases.
//            [$minValue, 'banana', true]
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MinIntValue
     */
    #[DataProvider('provideMinIntValueCases')]
    public function testValidation(int $minValue, int $inputValue)
    {
        $rule = new MinIntValue($minValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }


    public static function provideMinIntValueErrors()
    {
        $minValue = 100;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, $underValue],

//            // TODO - think about these cases.
//            [$minValue, 'banana', true]
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MinIntValue
     */
    #[DataProvider('provideMinIntValueErrors')]
    public function testErrors(int $minValue, int $inputValue)
    {
        $rule = new MinIntValue($minValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $inputValue);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::INT_TOO_SMALL,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString($validationResult, (string)$minValue);
    }

    /**
     * @covers \DataType\ProcessRule\MinIntValue
     */
    public function testDescription()
    {
        $minValue = 20;
        $rule = new MinIntValue($minValue);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame($minValue, $description->getMinimum());
        $this->assertFalse($description->isExclusiveMinimum());
    }
}
