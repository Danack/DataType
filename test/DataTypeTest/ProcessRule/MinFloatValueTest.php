<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\MinFloatValue;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class MinFloatValueTest extends BaseTestCase
{
    public function provideMinFloatValueCases()
    {
        $minValue = 100.5;
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
     * @dataProvider provideMinFloatValueCases
     * @covers \DataType\ProcessRule\MinFloatValue
     */
    public function testValidation(float $minValue, float $inputValue)
    {
        $rule = new MinFloatValue($minValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }


    public function provideMinFloatValueErrors()
    {
        $minValue = 100.5;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, $underValue],

            // TODO - think about these cases.
//            [$minValue, 'banana']
        ];
    }

    /**
     * @dataProvider provideMinFloatValueErrors
     * @covers \DataType\ProcessRule\MinFloatValue
     */
    public function testErrors(float $minValue, float $inputValue)
    {
        $rule = new MinFloatValue($minValue);
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
     * @covers \DataType\ProcessRule\MinFloatValue
     */
    public function testDescription()
    {
        $minValue = 20.0;
        $rule = new MinFloatValue($minValue);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame($minValue, $description->getMinimum());
        $this->assertFalse($description->isExclusiveMinimum());
    }
}
