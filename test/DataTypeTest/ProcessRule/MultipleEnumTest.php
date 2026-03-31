<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\MultipleEnum;
use DataType\Value\MultipleEnums;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class MultipleEnumTest extends BaseTestCase
{

    public static function providesMultipleEnumWorks()
    {
        return [
            ['foo', ['foo']],
            ['bar,foo', ['bar', 'foo']],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MultipleEnum
     * @param array<int, string> $expectedResult
     */
    #[DataProvider('providesMultipleEnumWorks')]
    public function testMultipleEnumWorks(string $inputString, array $expectedResult)
    {
        $rule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputString, $processedValues, $dataStorage
        );
        $this->assertNoProblems($validationResult);

        $validationValue = $validationResult->getValue();

        $this->assertInstanceOf(MultipleEnums::class, $validationValue);
        /** @var $validationValue \DataType\Value\MultipleEnums */

        $this->assertEquals($expectedResult, $validationValue->getValues());
    }

    /**
     * @covers \DataType\ProcessRule\MultipleEnum
     */
    public function testMultipleEnumErrors()
    {
        $badValue = 'zot';
        $rule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $badValue,
            $processedValues,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $badValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ENUM_MAP_UNRECOGNISED_VALUE_MULTIPLE,
            $validationResult->getValidationProblems()
        );
    }

    public static function provideMultipleEnumCases()
    {
        return [
            ['foo,', ['foo']],
            [',,,,,foo,', ['foo']],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MultipleEnum
     * @param array<int, string> $expectedOutput
     */
    #[DataProvider('provideMultipleEnumCases')]
    public function testMultipleEnum_emptySegments(string $input, array $expectedOutput)
    {
        $enumRule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $result = $enumRule->process(
            $input, $processedValues, $dataStorage
        );

        $this->assertEmpty($result->getValidationProblems());
        $value = $result->getValue();
        $this->assertInstanceOf(MultipleEnums::class, $value);
        $this->assertEquals($expectedOutput, $value->getValues());
    }

    // TODO - these appear to be duplicate tests.
    public static function provideTestCases()
    {
        return [
            ['time', ['time']],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MultipleEnum
     * @param array<int, string> $expectedMultipleEnumValues
     */
    #[DataProvider('provideTestCases')]
    public function testValidation(string $testValue, array $expectedMultipleEnumValues)
    {
        $rule = new MultipleEnum(['time', 'distance']);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $value = $validationResult->getValue();
        $this->assertInstanceOf(MultipleEnums::class, $value);

        /** @var $value \DataType\Value\MultipleEnums */
        $this->assertEquals($expectedMultipleEnumValues, $value->getValues());
    }


    public static function provideTestErrors()
    {
        yield ['bar'];
    }

    /**
     * @covers \DataType\ProcessRule\MultipleEnum
     */
    #[DataProvider('provideTestErrors')]
    public function testErrors(string $testValue)
    {
        $values = ['time', 'distance'];

        $rule = new MultipleEnum($values);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ENUM_MAP_UNRECOGNISED_VALUE_MULTIPLE,
            $validationResult->getValidationProblems()
        );
        $this->assertOneErrorAndContainsString(
            $validationResult,
            implode(", ", $values)
        );
    }

    /**
     * @covers \DataType\ProcessRule\MultipleEnum
     */
    public function testDescription()
    {
        $values = ['time', 'distance'];

        $rule = new MultipleEnum($values);
        $description = $this->applyRuleToDescription($rule);
    }
}
