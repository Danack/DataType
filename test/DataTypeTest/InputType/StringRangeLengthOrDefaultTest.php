<?php

declare(strict_types=1);

namespace DataTypeTest\InputType;

use DataType\Messages;
use DataType\ProcessedValues;
use DataType\InputType\IntRangeOrDefault;
use DataTypeTest\BaseTestCase;
use DataType\InputType\StringRangeLengthOrDefault;
use DataType\DataStorage\TestArrayDataStorage;
use function DataType\processInputTypeWithDataStorage;

/**
 * @coversNothing
 */
class StringRangeLengthOrDefaultTest extends BaseTestCase
{
    function provideTestWorks()
    {
        $string = "tenletters";

        yield [10, 100, $string];
        yield [10, 100, str_repeat($string, 5)];
        yield [10, 100, str_repeat($string, 10)];
    }

    /**
     * @covers \DataType\InputType\StringRangeLengthOrDefault
     * @dataProvider provideTestWorks
     */
    public function testWorks(int $minimum, int $maximum, $expected_value)
    {
        $intRange = new StringRangeLengthOrDefault(
            $minimum,
            $maximum,
            $name = 'foo',
            "Some other string"
        );

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueButRoot(
            'foo',
            $expected_value
        );

        $validationProblems = processInputTypeWithDataStorage(
            $intRange->getInputType(),
            $processedValues,
            $dataStorage
        );

        $this->assertCount(0, $validationProblems);
        [$result_value, $was_found] = $processedValues->getValueForTargetProperty('foo');
        $this->assertSame(true, $was_found);
        $this->assertSame($expected_value, $result_value);
    }

    /**
     * @covers \DataType\InputType\StringRangeLengthOrDefault
     */
    public function testWorksWithDefault()
    {
        $default_value = "Hello, I am a string that is over 10 letters in length, but less than 100.";

        $intRange = new StringRangeLengthOrDefault(
            $minimumLength = 10,
            $maximumLength = 100,
            $name = 'foo',
            $default_value
        );
        $inputType = $intRange->getInputType();

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::createMissing('foo');

        $validationProblems = processInputTypeWithDataStorage(
            $inputType,
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
        $string = "tenletters";
        yield [10, 100, "tooshort", Messages::STRING_TOO_SHORT];
        yield [10, 100, str_repeat($string, 12), Messages::STRING_TOO_LONG];
    }

    /**
     * @covers \DataType\InputType\StringRangeLengthOrDefault
     * @dataProvider provideTestErrors
     */
    public function testErrors(int $minimum, int $maximum, $input_value, $expected_message)
    {
        $intRange = new StringRangeLengthOrDefault(
            $minimum,
            $maximum,
            $name = 'foo',
            'some other string'
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
