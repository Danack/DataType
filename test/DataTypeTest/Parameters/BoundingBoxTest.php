<?php

declare(strict_types=1);

namespace DataTypeTest\Parameters;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Messages;
use DataType\Parameters\BoundingBox;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Parameters\BoundingBox
 * @covers \DataType\ProcessRule\GreaterOrEqualParam
 * @covers \DataType\Basic\LatitudeFloat
 * @covers \DataType\Basic\LongitudeFloat
 */
class BoundingBoxTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float, float, float, float}>
     */
    public static function provides_parses_input_to_expected_bounds(): \Generator
    {
        yield 'valid box' => [
            [
                'min_latitude' => 51.0,
                'max_latitude' => 52.0,
                'min_longitude' => -1.0,
                'max_longitude' => 1.0,
            ],
            51.0,
            52.0,
            -1.0,
            1.0,
        ];
        yield 'degenerate point box' => [
            [
                'min_latitude' => 51.4545,
                'max_latitude' => 51.4545,
                'min_longitude' => -0.4545,
                'max_longitude' => -0.4545,
            ],
            51.4545,
            51.4545,
            -0.4545,
            -0.4545,
        ];
        yield 'world bounds' => [
            [
                'min_latitude' => -90.0,
                'max_latitude' => 90.0,
                'min_longitude' => -180.0,
                'max_longitude' => 180.0,
            ],
            -90.0,
            90.0,
            -180.0,
            180.0,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_parses_input_to_expected_bounds')]
    public function test_parses_input_to_expected_bounds(
        array $data,
        float $expectedMinLatitude,
        float $expectedMaxLatitude,
        float $expectedMinLongitude,
        float $expectedMaxLongitude
    ): void {
        $result = BoundingBox::createFromVarMap(new ArrayVarMap($data));

        $this->assertSame($expectedMinLatitude, $result->min_latitude);
        $this->assertSame($expectedMaxLatitude, $result->max_latitude);
        $this->assertSame($expectedMinLongitude, $result->min_longitude);
        $this->assertSame($expectedMaxLongitude, $result->max_longitude);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'max latitude less than min' => [
            [
                'min_latitude' => 52.0,
                'max_latitude' => 51.0,
                'min_longitude' => -1.0,
                'max_longitude' => 1.0,
            ],
            '/max_latitude',
            Messages::VALUE_MUST_BE_GREATER_OR_EQUAL_TO_PARAM,
        ];
        yield 'max longitude less than min' => [
            [
                'min_latitude' => 51.0,
                'max_latitude' => 52.0,
                'min_longitude' => 1.0,
                'max_longitude' => -1.0,
            ],
            '/max_longitude',
            Messages::VALUE_MUST_BE_GREATER_OR_EQUAL_TO_PARAM,
        ];
        yield 'missing min latitude' => [
            [
                'max_latitude' => 52.0,
                'min_longitude' => -1.0,
                'max_longitude' => 1.0,
            ],
            '/min_latitude',
            Messages::VALUE_NOT_SET,
        ];
        yield 'min latitude out of range' => [
            [
                'min_latitude' => -90.1,
                'max_latitude' => 52.0,
                'min_longitude' => -1.0,
                'max_longitude' => 1.0,
            ],
            '/min_latitude',
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
            BoundingBox::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}
