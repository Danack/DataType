<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessRule\GreaterOrEqualParam;
use DataTypeTest\BaseTestCase;

/**
 * @covers \DataType\ProcessRule\GreaterOrEqualParam
 */
class GreaterOrEqualParamTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float, float}>
     */
    public static function provides_process_passes(): \Generator
    {
        yield 'equal' => [['min_latitude' => 51.0], 51.0, 51.0];
        yield 'greater' => [['min_latitude' => 51.0], 52.5, 52.5];
    }

    /**
     * @param array<string, mixed> $processedValuesData
     */
    #[DataProvider('provides_process_passes')]
    public function test_process_passes(
        array $processedValuesData,
        float $value,
        float $expectedValue
    ): void {
        $processedValues = createProcessedValuesFromArray($processedValuesData);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('max_latitude', $value);

        $rule = new GreaterOrEqualParam('min_latitude');
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertNoProblems($validationResult);
        $this->assertSame($expectedValue, $validationResult->getValue());
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, float, string, string}>
     */
    public static function provides_process_errors(): \Generator
    {
        yield 'less than previous' => [
            ['min_latitude' => 52.0],
            51.0,
            '/max_latitude',
            Messages::VALUE_MUST_BE_GREATER_OR_EQUAL_TO_PARAM,
        ];
        yield 'missing previous param' => [
            [],
            51.0,
            '/max_latitude',
            Messages::ERROR_NO_PREVIOUS_PARAMETER,
        ];
    }

    /**
     * @param array<string, mixed> $processedValuesData
     */
    #[DataProvider('provides_process_errors')]
    public function test_process_errors(
        array $processedValuesData,
        float $value,
        string $path,
        string $messagePattern
    ): void {
        $processedValues = createProcessedValuesFromArray($processedValuesData);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('max_latitude', $value);

        $rule = new GreaterOrEqualParam('min_latitude');
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp($path, $messagePattern, $validationResult->getValidationProblems());
        $this->assertCount(1, $validationResult->getValidationProblems());
    }

    public function test_description(): void
    {
        $rule = new GreaterOrEqualParam('min_latitude');
        $description = $this->applyRuleToDescription($rule);

        $this->assertNotNull($description->getDescription());
        $this->assertStringMatchesTemplateString(
            Messages::VALUE_MUST_BE_GREATER_OR_EQUAL_TO_PARAM,
            $description->getDescription()
        );
        $this->assertStringContainsString('min_latitude', $description->getDescription());
    }
}
