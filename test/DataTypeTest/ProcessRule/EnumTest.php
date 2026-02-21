<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\Enum;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class EnumTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['zoq',  'zoq'],
            ['12345', '12345'],

//            ['Zebranky ', true, null],
//            [12345, true, null]
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \DataType\ProcessRule\Enum
     * @param mixed $testValue
     * @param mixed $expectedValue
     */
    public function testWorks($testValue, $expectedValue)
    {
        $enumValues = ['zoq', 'fot', 'pik', '12345'];

        $rule = new Enum($enumValues);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrors()
    {
        yield ['Zebranky '];
        yield [12345, ];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\Enum
     * @param mixed $testValue
     */
    public function testValidationErrors($testValue)
    {
        $enumValues = ['zoq', 'fot', 'pik', '12345'];

        $rule = new Enum($enumValues);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ProcessRule\Enum
     */
    public function testDescription()
    {
        $enumValues = ['zoq', 'fot', 'pik', '12345'];
        $rule = new Enum($enumValues);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame($enumValues, $description->getEnumValues());
    }
}
