<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\Exception\InvalidDatetimeFormatExceptionData;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetDatetime;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetDatetimeTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetDatetime
     */
    public function testMissingGivesError()
    {
        $rule = new GetDatetime();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::VALUE_NOT_SET,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetDatetime
     */
    public function testInvalidDateTimeTypeErrors()
    {
        $message = sprintf(
            \DataType\Messages::ERROR_DATE_FORMAT_MUST_BE_STRING,
            'NULL',
            1
        );

        $this->expectExceptionMessage($message);
        $rule = new GetDatetime([
            \DateTime::COOKIE,
            null
        ]);
    }

    public function providesValidationWorks()
    {
        yield [
            '2002-10-02T10:00:00-05:00',
            \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, '2002-10-02T10:00:00-05:00')
        ];
    }


    /**
     * @dataProvider providesValidationWorks
     * @covers \DataType\ExtractRule\GetDatetime
     */
    public function testValidationWorks($inputValue, $expectedValue)
    {
        $rule = new GetDatetime();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }


    /**
     * @covers \DataType\ExtractRule\GetDatetime
     */
    public function testInvalidDatetimeFormat()
    {
        $index = 'foo';

        $allowedFormats = [\DateTime::RFC3339, 5];

        $this->expectException(InvalidDatetimeFormatExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::ERROR_DATE_FORMAT_MUST_BE_STRING);

        $rule = new GetDatetime($allowedFormats);
    }


    /**
     * @covers \DataType\ExtractRule\GetDatetime
     */
    public function testInvalidDatetimeInput()
    {
        $allowedFormats = [\DateTime::RFC3339];
        $rule = new GetDatetime($allowedFormats);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 'Some invalid string');

        $validationResult = $rule->process(
            new ProcessedValues(),
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_INVALID_DATETIME,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetDatetime
     */
    public function testFromArrayErrors()
    {
        $index = 'foo';

        $data = [$index => [1, 2, 3]];

        $rule = new GetDatetime();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue($data)
        );

        $this->assertValidationProblemRegexp(
            '/' . $index,
            Messages::ERROR_DATETIME_MUST_START_AS_STRING,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ExtractRule\GetDatetime
     */
    public function testFromObjectErrors()
    {
        $index = 'foo';

        $data = [$index => new \stdClass()];

        $rule = new GetDatetime();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue($data)
        );

        $this->assertValidationProblemRegexp(
            '/' . $index,
            Messages::ERROR_DATETIME_MUST_START_AS_STRING,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ExtractRule\GetDatetime
     */
    public function testDescription()
    {
        $rule = new GetDatetime();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertTrue($description->getRequired());
    }
}
