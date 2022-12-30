<?php

declare(strict_types=1);

namespace TypeSpecTest\PropertyInputTypeSpec;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\DataType;
use TypeSpec\Messages;
use TypeSpec\ProcessedValues;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\AlwaysErrorsRule;
use TypeSpec\DataType\IntRangeOrDefault;
use TypeSpec\DataStorage\TestArrayDataStorage;
use function TypeSpec\processDataTypeWithDataStorage;

/**
 * @coversNothing
 */
class IntRangeOrDefaultTest extends BaseTestCase
{
    function provideTestWorks()
    {
        yield [0, 100, 0];
        yield [0, 100, 50];
        yield [0, 100, 100];
    }

    /**
     * @covers \TypeSpec\DataType\IntRangeOrDefault
     * @dataProvider provideTestWorks
     */
    public function testWorks(int $minimum, int $maximum, $expected_value)
    {
        $intRange = new IntRangeOrDefault(
            $minimum,
            $maximum,
            $name = 'foo',
            $expected_value
        );

        $typeSpec = $intRange->getDataType();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::createEmptyAtRoot();

        $validationProblems = processDataTypeWithDataStorage(
            $typeSpec,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(0, $validationProblems);
        [$result_value, $was_found] = $processedValues->getValueForTargetProperty('foo');
        $this->assertSame(true, $was_found);
        $this->assertSame($expected_value, $result_value);
    }

    /**
     * @covers \TypeSpec\DataType\IntRangeOrDefault
     */
    public function testWorksWithDefault()
    {
        $default_value = 50;
        $minimum = 0;
        $maximum = 100;

        $intRange = new IntRangeOrDefault(
            $minimum,
            $maximum,
            $name = 'foo',
            $default_value
        );
        $typeSpec = $intRange->getDataType();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::createMissing('foo');

        $validationProblems = processDataTypeWithDataStorage(
            $typeSpec,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(0, $validationProblems);
        [$result_value, $was_found] = $processedValues->getValueForTargetProperty('foo');
        $this->assertSame(true, $was_found);
        $this->assertSame($default_value, $result_value);
    }


    function provideTestErrors()
    {
        yield [0, 100, -1, Messages::INT_TOO_SMALL];
        yield [0, 100, 101, Messages::INT_TOO_LARGE];
    }

    /**
     * @covers \TypeSpec\DataType\IntRangeOrDefault
     * @dataProvider provideTestErrors
     */
    public function testErrors(int $minimum, int $maximum, $expected_value, $expected_message)
    {
        $intRange = new IntRangeOrDefault(
            $minimum,
            $maximum,
            $name = 'foo',
            $expected_value
        );

        $typeSpec = $intRange->getDataType();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueButRoot(
            'foo',
            $expected_value
        );

        $validationProblems = processDataTypeWithDataStorage(
            $typeSpec,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/foo',
            $expected_message,
            $validationProblems
        );
    }
}
