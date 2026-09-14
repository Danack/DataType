<?php

declare(strict_types=1);

namespace DataTypeTest\Parameters;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Messages;
use DataType\Parameters\GpsParams;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Parameters\GpsParams
 */
class GpsParamsTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float, float}>
     */
    public static function provides_parses_input_to_expected_latitude_and_longitude(): \Generator
    {
        yield 'both set' => [
            ['latitude' => 51.4545, 'longitude' => -0.4545],
            51.4545,
            -0.4545,
        ];
        yield 'boundary values' => [
            ['latitude' => 90.0, 'longitude' => 180.0],
            90.0,
            180.0,
        ];
        yield 'negative boundary values' => [
            ['latitude' => -90.0, 'longitude' => -180.0],
            -90.0,
            -180.0,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_parses_input_to_expected_latitude_and_longitude')]
    public function test_parses_input_to_expected_latitude_and_longitude(
        array $data,
        float $expectedLatitude,
        float $expectedLongitude
    ): void {
        $result = GpsParams::createFromVarMap(new ArrayVarMap($data));

        $this->assertSame($expectedLatitude, $result->latitude);
        $this->assertSame($expectedLongitude, $result->longitude);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'missing latitude' => [
            ['longitude' => -0.4545],
            '/latitude',
            Messages::VALUE_NOT_SET,
        ];
        yield 'missing longitude' => [
            ['latitude' => 51.4545],
            '/longitude',
            Messages::VALUE_NOT_SET,
        ];
        yield 'latitude out of range' => [
            ['latitude' => 90.1, 'longitude' => 0.0],
            '/latitude',
            Messages::FLOAT_TOO_LARGE,
        ];
        yield 'longitude out of range' => [
            ['latitude' => 0.0, 'longitude' => -180.1],
            '/longitude',
            Messages::FLOAT_TOO_SMALL,
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
            GpsParams::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\Runtime\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}
