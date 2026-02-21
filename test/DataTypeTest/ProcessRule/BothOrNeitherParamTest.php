<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessRule\BothOrNeitherParam;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class BothOrNeitherParamTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float|null, float|null}>
     */
    public static function provides_process_passes(): \Generator
    {
        yield 'both null' => [['latitude' => null], null, null];
        yield 'both set' => [['latitude' => 51.0], -0.5, -0.5];
    }

    /**
     * @dataProvider provides_process_passes
     * @param array<string, mixed> $processedValuesData
     * @covers \DataType\ProcessRule\BothOrNeitherParam
     */
    public function test_process_passes(
        array $processedValuesData,
        float|null $value,
        float|null $expectedValue
    ): void {
        $processedValues = createProcessedValuesFromArray($processedValuesData);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('longitude', $value);

        $rule = new BothOrNeitherParam('latitude');
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertNoProblems($validationResult);
        $this->assertSame($expectedValue, $validationResult->getValue());
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, float|null, string, string}>
     */
    public static function provides_process_errors(): \Generator
    {
        yield 'current null other set' => [
            ['latitude' => 51.0],
            null,
            '/longitude',
            Messages::PAIR_PARAM_BOTH_OR_NEITHER,
        ];
        yield 'current set other null' => [
            ['latitude' => null],
            -0.5,
            '/longitude',
            Messages::PAIR_PARAM_BOTH_OR_NEITHER,
        ];
        yield 'missing previous param' => [
            [],
            -0.5,
            '/longitude',
            Messages::PAIR_PARAM_BOTH_OR_NEITHER,
        ];
    }

    /**
     * @dataProvider provides_process_errors
     * @param array<string, mixed> $processedValuesData
     * @covers \DataType\ProcessRule\BothOrNeitherParam
     */
    public function test_process_errors(
        array $processedValuesData,
        float|null $value,
        string $path,
        string $messagePattern
    ): void {
        $processedValues = createProcessedValuesFromArray($processedValuesData);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('longitude', $value);

        $rule = new BothOrNeitherParam('latitude');
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp($path, $messagePattern, $validationResult->getValidationProblems());
        $this->assertCount(1, $validationResult->getValidationProblems());
    }

    /**
     * @covers \DataType\ProcessRule\BothOrNeitherParam
     */
    public function test_description(): void
    {
        $rule = new BothOrNeitherParam('latitude');
        $description = $this->applyRuleToDescription($rule);

        $this->assertNotNull($description->getDescription());
        $this->assertStringMatchesTemplateString(
            Messages::PAIR_PARAM_BOTH_OR_NEITHER,
            $description->getDescription()
        );
        $this->assertStringContainsString('latitude', $description->getDescription());
    }
}
