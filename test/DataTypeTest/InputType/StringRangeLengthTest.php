<?php

declare(strict_types=1);

namespace DataTypeTest\InputType;

use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;
use DataType\InputType\StringRangeLength;
use DataType\DataStorage\TestArrayDataStorage;
use function DataType\processInputTypeWithDataStorage;

/**
 * @coversNothing
 */
class StringRangeLengthTest extends BaseTestCase
{
    function provideTestWorks()
    {
        $string = "tenletters";

        yield [10, 100, $string];
        yield [10, 100, str_repeat($string, 5)];
        yield [10, 100, str_repeat($string, 10)];
    }

    /**
     * @covers \DataType\InputType\StringRangeLength
     * @dataProvider provideTestWorks
     */
    public function testWorks(int $minimum, int $maximum, $expected_value)
    {
        $intRange = new StringRangeLength(
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
        $string = "tenletters";
        yield [10, 100, "tooshort", Messages::STRING_TOO_SHORT];
        yield [10, 100, str_repeat($string, 12), Messages::STRING_TOO_LONG];
    }

    /**
     * @covers \DataType\InputType\StringRangeLength
     * @dataProvider provideTestErrors
     */
    public function testErrors(int $minimum, int $maximum, $input_value, $expected_message)
    {
        $intRange = new StringRangeLength(
            $minimum,
            $maximum,
            $name = 'foo'
        );

        $inputType = $intRange->getInputType();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueButRoot(
            'foo',
            $input_value
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
