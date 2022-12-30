<?php

declare(strict_types=1);

namespace TypeSpecTest\PropertyInputTypeSpec;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\DataType;
use TypeSpec\Messages;
use TypeSpec\ProcessedValues;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\AlwaysErrorsRule;
use TypeSpec\DataType\IntRange;
use TypeSpec\DataStorage\TestArrayDataStorage;
use function TypeSpec\processDataTypeWithDataStorage;

/**
 * @coversNothing
 */
class IntRangeTest extends BaseTestCase
{
    function provideTestWorks()
    {
        yield [0, 100, 0];
        yield [0, 100, 50];
        yield [0, 100, 100];
    }

    /**
     * @covers \TypeSpec\DataType\IntRange
     * @dataProvider provideTestWorks
     */
    public function testWorks(int $minimum, int $maximum, $expected_value)
    {
        $intRange = new IntRange(
            $minimum,
            $maximum,
            $name = 'foo'
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

        $this->assertCount(0, $validationProblems);
        [$result_value, $was_found] = $processedValues->getValueForTargetProperty('foo');
        $this->assertSame(true, $was_found);
        $this->assertSame($expected_value, $result_value);
    }

    function provideTestErrors()
    {
        yield [0, 100, -1, Messages::INT_TOO_SMALL];
        yield [0, 100, 101, Messages::INT_TOO_LARGE];
    }

    /**
     * @covers \TypeSpec\DataType\IntRange
     * @dataProvider provideTestErrors
     */
    public function testErrors(int $minimum, int $maximum, $expected_value, $expected_message)
    {
        $intRange = new IntRange(
            $minimum,
            $maximum,
            $name = 'foo'
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
