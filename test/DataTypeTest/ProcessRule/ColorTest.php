<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\IsRgbColor;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class ColorTest extends BaseTestCase
{
    public function provideRgbColorWorks()
    {
        return [
            ['rgb(255, 255, 0)'],
            ['rgb(255,255,0)'],
        ];
    }

    /**
     * @dataProvider provideRgbColorWorks
     * @covers \DataType\ProcessRule\IsRgbColor
     */
    public function testValidation(string $inputString)
    {
        $rule = new IsRgbColor();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputString, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }

    public function provideRgbColorErrors()
    {
        return [
            ['rgb(255, 255, )', Messages::BAD_COLOR_STRING],
        ];
    }


    /**
     * @dataProvider provideRgbColorErrors
     * @covers \DataType\ProcessRule\IsRgbColor
     */
    public function testErrors(string $testValue, string $message)
    {
        $rule = new IsRgbColor();
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
     * @covers \DataType\ProcessRule\IsRgbColor
     */
    public function testDescription()
    {
        $rule = new IsRgbColor();
        $description = $this->applyRuleToDescription($rule);

//        $this->assertSame(0, $description->getMinimum());
//        $this->assertFalse($description->getExclusiveMinimum());
    }
}
