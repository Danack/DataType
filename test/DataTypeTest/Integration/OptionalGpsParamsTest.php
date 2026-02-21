<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataTypeTest\Integration\OptionalGpsParams
 * @covers \DataType\ProcessRule\BothOrNeitherParam
 */
class OptionalGpsParamsTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float|null, float|null}>
     */
    public static function provides_parses_input_to_expected_latitude_and_longitude(): \Generator
    {
        yield 'both set' => [
            ['latitude' => 51.4545, 'longitude' => -0.4545],
            51.4545,
            -0.4545,
        ];
        yield 'both missing' => [[], null, null];
        yield 'both set boundary values' => [
            ['latitude' => 90.0, 'longitude' => 180.0],
            90.0,
            180.0,
        ];
    }

    /**
     * @dataProvider provides_parses_input_to_expected_latitude_and_longitude
     * @param array<string, mixed> $data
     */
    public function test_parses_input_to_expected_latitude_and_longitude(
        array $data,
        float|null $expectedLatitude,
        float|null $expectedLongitude
    ): void {
        $result = OptionalGpsParams::createFromVarMap(new ArrayVarMap($data));

        $this->assertSame($expectedLatitude, $result->latitude);
        $this->assertSame($expectedLongitude, $result->longitude);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_both_or_neither_error(): \Generator
    {
        yield 'latitude only' => [
            ['latitude' => 51.4545],
            '/longitude',
            Messages::PAIR_PARAM_BOTH_OR_NEITHER,
        ];
        yield 'longitude only' => [
            ['longitude' => -0.4545],
            '/longitude',
            Messages::PAIR_PARAM_BOTH_OR_NEITHER,
        ];
    }

    /**
     * @dataProvider provides_fails_with_both_or_neither_error
     * @param array<string, mixed> $data
     */
    public function test_fails_with_both_or_neither_error(
        array $data,
        string $path,
        string $messagePattern
    ): void {
        try {
            OptionalGpsParams::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $problems = $ve->getValidationProblems();
            $this->assertCount(1, $problems);
            $this->assertValidationProblemRegexp($path, $messagePattern, $problems);
        }
    }
}
