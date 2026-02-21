<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetDatetimeOrDefault;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetDatetimeOrDefaultTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetDatetimeOrDefault
     */
    public function testMissingGivesDefault()
    {
        $default = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $rule = new GetDatetimeOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($default, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetDatetimeOrDefault
     */
    public function testMissingGivesNullDefault()
    {
        $rule = new GetDatetimeOrDefault(null);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertNull($validationResult->getValue());
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
     * @covers \DataType\ExtractRule\GetDatetimeOrDefault
     * @param string $inputValue
     * @param \DateTimeImmutable $expectedValue
     */
    public function testValidationWorks(string $inputValue, \DateTimeImmutable $expectedValue)
    {
        $default = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $rule = new GetDatetimeOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    /**
     * @covers \DataType\ExtractRule\GetDatetimeOrDefault
     */
    public function testInvalidDatetimeInput()
    {
        $default = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $allowedFormats = [\DateTime::RFC3339];
        $rule = new GetDatetimeOrDefault($default, $allowedFormats);
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
     * @covers \DataType\ExtractRule\GetDatetimeOrDefault
     */
    public function testFromArrayErrors()
    {
        $index = 'foo';
        $default = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');

        $data = [$index => [1, 2, 3]];

        $rule = new GetDatetimeOrDefault($default);
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
     * @covers \DataType\ExtractRule\GetDatetimeOrDefault
     */
    public function testDescription()
    {
        $default = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $rule = new GetDatetimeOrDefault($default);
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertFalse($description->getRequired());
    }
}
