<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Exception\InvalidRulesExceptionData;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\PositiveInt;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class PositiveIntTest extends BaseTestCase
{
    /**
     * @return list<array{int, int}>
     */
    public function provideTestCasesWorks(): array
    {
        return [
            [5, 5],
            [0, 0],
            [PositiveInt::MAX_SANE_VALUE, PositiveInt::MAX_SANE_VALUE],
            [PositiveInt::MAX_SANE_VALUE - 1, PositiveInt::MAX_SANE_VALUE - 1],
        ];
    }

    /**
     * @dataProvider provideTestCasesWorks
     * @covers \DataType\ProcessRule\PositiveInt
     */
    public function testValidationWorks(int $testValue, int $expectedResult): void
    {
        $rule = new PositiveInt();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedResult);
    }

    /**
     * @return \Generator<string, array{mixed}>
     */
    public static function provideNonIntValues(): \Generator
    {
        yield 'string' => ['5'];
        yield 'float' => [5.5];
        yield 'string non-numeric' => ['banana'];
        yield 'null' => [null];
    }

    /**
     * @dataProvider provideNonIntValues
     * @covers \DataType\ProcessRule\PositiveInt
     */
    public function test_non_int_throws_invalid_rules_exception(mixed $testValue): void
    {
        $rule = new PositiveInt();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::BAD_TYPE_FOR_INT_PROCESS_RULE);
        $rule->process($testValue, $processedValues, $dataStorage);
    }

    /**
     * @return list<array{int, string}>
     */
    public function provideTestErrors(): array
    {
        return [
            [PositiveInt::MAX_SANE_VALUE + 1, Messages::INT_OVER_LIMIT],
        ];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\PositiveInt
     */
    public function testErrors(int $testValue, string $message): void
    {
        $rule = new PositiveInt();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ProcessRule\PositiveInt
     */
    public function testDescription(): void
    {
        $rule = new PositiveInt();
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(0, $description->getMinimum());
        $this->assertFalse($description->getExclusiveMinimum());
    }
}
