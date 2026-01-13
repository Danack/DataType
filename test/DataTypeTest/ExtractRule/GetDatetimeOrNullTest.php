<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetDatetimeOrNull;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetDatetimeOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetDatetimeOrNull
     */
    public function testMissingGivesError()
    {
        $rule = new GetDatetimeOrNull();
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
     * @covers \DataType\ExtractRule\GetDatetimeOrNull
     */
    public function testValidationWithNull()
    {
        $expectedValue = null;

        $rule = new GetDatetimeOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
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
     * @covers \DataType\ExtractRule\GetDatetimeOrNull
     * @param string $inputValue
     * @param \DateTimeImmutable $expectedValue
     */
    public function testValidationWorks(string $inputValue, \DateTimeImmutable $expectedValue)
    {
        $rule = new GetDatetimeOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    /**
     * @covers \DataType\ExtractRule\GetDatetimeOrNull
     */
    public function testInvalidDatetimeInput()
    {
        $allowedFormats = [\DateTime::RFC3339];
        $rule = new GetDatetimeOrNull($allowedFormats);
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
     * @covers \DataType\ExtractRule\GetDatetimeOrNull
     */
    public function testFromArrayErrors()
    {
        $index = 'foo';

        $data = [$index => [1, 2, 3]];

        $rule = new GetDatetimeOrNull();
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
     * @covers \DataType\ExtractRule\GetDatetimeOrNull
     */
    public function testDescription()
    {
        $rule = new GetDatetimeOrNull();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertTrue($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
