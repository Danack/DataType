<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessRule\ImagickIsRgbColor;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\ValidDate;
use DataType\ProcessedValues;
use DataType\Messages;

/**
 * @coversNothing
 */
class ValidDateTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        return [
            [
                '2002-10-02',
                \DateTime::createFromFormat('Y-m-d', '2002-10-02')->setTime(0, 0, 0, 0)
            ],
            [
                '2002-10-02',
                \DateTime::createFromFormat('Y-m-d', '2002-10-02')->setTime(0, 0, 0, 0)
            ],
        ];
    }


    /**
     * @dataProvider provideTestWorksCases
     * @covers \DataType\ProcessRule\ValidDate
     */
    public function testValidationWorks($input, $expectedTime)
    {
        $rule = new ValidDate();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $input, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedTime);
    }

    public function provideTestErrorsCases()
    {
        return [
            ['2pm on Tuesday'],
            ['Banana'],
        ];
    }

    /**
     * @dataProvider provideTestErrorsCases
     * @covers \DataType\ProcessRule\ValidDate
     */
    public function testValidationErrors($input)
    {
        $rule = new ValidDate();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $input,
            $processedValues,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_INVALID_DATETIME,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\ValidDate
     */
    public function testDescription()
    {
        $rule = new ValidDate();
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(ParamDescription::FORMAT_DATE, $description->getFormat());
        $this->assertSame(ParamDescription::TYPE_STRING, $description->getType());
    }
}
