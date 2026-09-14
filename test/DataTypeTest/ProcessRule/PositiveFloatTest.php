<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Exception\Logic\InvalidRulesExceptionData;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\PositiveFloat;
use DataTypeTest\BaseTestCase;

/**
 * @covers \DataType\ProcessRule\PositiveFloat
 */
class PositiveFloatTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{float, float}>
     */
    public static function provides_validation_works(): \Generator
    {
        yield 'positive' => [5.5, 5.5];
        yield 'zero' => [0.0, 0.0];
        yield 'max sane' => [
            (float) PositiveFloat::MAX_SANE_VALUE,
            (float) PositiveFloat::MAX_SANE_VALUE,
        ];
        yield 'just under max' => [
            (float) (PositiveFloat::MAX_SANE_VALUE - 1),
            (float) (PositiveFloat::MAX_SANE_VALUE - 1),
        ];
    }

    #[DataProvider('provides_validation_works')]
    public function test_validation_works(float $testValue, float $expectedResult): void
    {
        $rule = new PositiveFloat();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame($expectedResult, $validationResult->getValue());
    }

    /**
     * @return \Generator<string, array{mixed}>
     */
    public static function provides_non_float_values(): \Generator
    {
        yield 'string' => ['5.5'];
        yield 'int' => [5];
        yield 'string non-numeric' => ['banana'];
        yield 'null' => [null];
    }

    #[DataProvider('provides_non_float_values')]
    public function test_non_float_throws_invalid_rules_exception(mixed $testValue): void
    {
        $rule = new PositiveFloat();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::BAD_TYPE_FOR_FLOAT_PROCESS_RULE);
        $rule->process($testValue, $processedValues, $dataStorage);
    }

    /**
     * @return \Generator<string, array{float, string}>
     */
    public static function provides_errors(): \Generator
    {
        yield 'negative' => [-0.1, Messages::FLOAT_TOO_SMALL];
        yield 'over max' => [
            (float) (PositiveFloat::MAX_SANE_VALUE + 1),
            Messages::FLOAT_TOO_LARGE,
        ];
    }

    #[DataProvider('provides_errors')]
    public function test_errors(float $testValue, string $message): void
    {
        $rule = new PositiveFloat();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }

    public function test_description(): void
    {
        $rule = new PositiveFloat();
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(0.0, $description->getMinimum());
        $this->assertFalse($description->getExclusiveMinimum());
        $this->assertSame((float) PositiveFloat::MAX_SANE_VALUE, $description->getMaximum());
        $this->assertFalse($description->getExclusiveMaximum());
    }
}
