<?php

declare(strict_types=1);

namespace DataTypeTest\InputType;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\InputType\IntRange;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;
use function DataType\processInputTypeWithDataStorage;

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
     * @covers \DataType\InputType\IntRange
     * @dataProvider provideTestWorks
     */
    public function testWorks(int $minimum, int $maximum, int $expected_value)
    {
        $intRange = new IntRange(
            $minimum,
            $maximum,
            $name = 'foo'
        );

        $inputType = $intRange->getInputType();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueButRoot(
            'foo',
            $expected_value
        );

        $validationProblems = processInputTypeWithDataStorage(
            $inputType,
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
     * @covers \DataType\InputType\IntRange
     * @dataProvider provideTestErrors
     */
    public function testErrors(int $minimum, int $maximum, int $expected_value, string $expected_message)
    {
        $intRange = new IntRange(
            $minimum,
            $maximum,
            $name = 'foo'
        );

        $inputType = $intRange->getInputType();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueButRoot(
            'foo',
            $expected_value
        );

        $validationProblems = processInputTypeWithDataStorage(
            $inputType,
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
