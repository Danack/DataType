<?php

declare(strict_types=1);

namespace DataTypeTest\Parameters;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Messages;
use DataType\Parameters\GeoRadius;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Parameters\GeoRadius
 * @covers \DataType\Basic\RadiusMetresFloat
 */
class GeoRadiusTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float, float, float}>
     */
    public static function provides_parses_input_to_expected_values(): \Generator
    {
        yield 'typical values' => [
            ['latitude' => 51.4545, 'longitude' => -0.4545, 'radius_metres' => 1500.0],
            51.4545,
            -0.4545,
            1500.0,
        ];
        yield 'zero radius' => [
            ['latitude' => 0.0, 'longitude' => 0.0, 'radius_metres' => 0.0],
            0.0,
            0.0,
            0.0,
        ];
        yield 'string numbers' => [
            ['latitude' => '51.4545', 'longitude' => '-0.4545', 'radius_metres' => '250'],
            51.4545,
            -0.4545,
            250.0,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_parses_input_to_expected_values')]
    public function test_parses_input_to_expected_values(
        array $data,
        float $expectedLatitude,
        float $expectedLongitude,
        float $expectedRadiusMetres
    ): void {
        $result = GeoRadius::createFromVarMap(new ArrayVarMap($data));

        $this->assertSame($expectedLatitude, $result->latitude);
        $this->assertSame($expectedLongitude, $result->longitude);
        $this->assertSame($expectedRadiusMetres, $result->radius_metres);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'missing radius' => [
            ['latitude' => 51.4545, 'longitude' => -0.4545],
            '/radius_metres',
            Messages::VALUE_NOT_SET,
        ];
        yield 'negative radius' => [
            ['latitude' => 51.4545, 'longitude' => -0.4545, 'radius_metres' => -1.0],
            '/radius_metres',
            Messages::FLOAT_TOO_SMALL,
        ];
        yield 'latitude out of range' => [
            ['latitude' => 91.0, 'longitude' => 0.0, 'radius_metres' => 100.0],
            '/latitude',
            Messages::FLOAT_TOO_LARGE,
        ];
        yield 'missing longitude' => [
            ['latitude' => 51.4545, 'radius_metres' => 100.0],
            '/longitude',
            Messages::VALUE_NOT_SET,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_fails_with_validation_error')]
    public function test_fails_with_validation_error(
        array $data,
        string $path,
        string $messagePattern
    ): void {
        try {
            GeoRadius::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\Runtime\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}
