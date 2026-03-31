<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Basic\OptionalLatitudeFloat;
use DataType\Basic\OptionalLongitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\OptionalLongitudeFloatFixture;
use DataTypeTestFixture\Basic\OptionalLongitudeFloatWithPairFixture;

/**
 * @covers \DataType\Basic\OptionalLongitudeFloat
 */
class OptionalLongitudeFloatTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float|null}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'float' => [['lng' => -0.4545], -0.4545];
        yield 'string float' => [['lng' => '-0.4545'], -0.4545];
        yield 'missing gives null' => [[], null];
        yield 'boundary min' => [['lng' => -180.0], -180.0];
        yield 'boundary max' => [['lng' => 180.0], 180.0];
        yield 'zero' => [['lng' => 0.0], 0.0];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_works_parses_input_to_expected')]
    public function test_works_parses_input_to_expected(array $data, float|null $expected): void
    {
        $result = OptionalLongitudeFloatFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'invalid type' => [['lng' => 'not a number'], '/lng', 'floating point number'];
        yield 'null' => [['lng' => null], '/lng', Messages::FLOAT_REQUIRED_WRONG_TYPE];
        yield 'too small' => [['lng' => -180.1], '/lng', Messages::FLOAT_TOO_SMALL];
        yield 'too large' => [['lng' => 180.1], '/lng', Messages::FLOAT_TOO_LARGE];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_fails_with_validation_error')]
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            OptionalLongitudeFloatFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        }
        catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }

    public function test_implements_has_input_type(): void
    {
        $propertyType = new OptionalLongitudeFloat('test_name');
        $this->assertInstanceOf(\DataType\HasInputType::class, $propertyType);
    }

    public function test_get_input_type_returns_correct_type(): void
    {
        $propertyType = new OptionalLongitudeFloat('test_name');
        $inputType = $propertyType->getInputType();

        $this->assertInstanceOf(\DataType\InputType::class, $inputType);
        $this->assertSame('test_name', $inputType->getName());
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, float|null}>
     */
    public static function provides_with_pair_param_parses_input_to_expected(): \Generator
    {
        yield 'both set' => [['latitude' => 51.4545, 'longitude' => -0.4545], -0.4545];
        yield 'both missing' => [[], null];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_with_pair_param_parses_input_to_expected')]
    public function test_with_pair_param_parses_input_to_expected(array $data, float|null $expected): void
    {
        $result = OptionalLongitudeFloatWithPairFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_with_pair_param_fails_with_validation_error(): \Generator
    {
        yield 'longitude only' => [['longitude' => -0.4545], '/longitude', Messages::PAIR_PARAM_BOTH_OR_NEITHER];
        yield 'latitude only' => [['latitude' => 51.4545], '/longitude', Messages::PAIR_PARAM_BOTH_OR_NEITHER];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_with_pair_param_fails_with_validation_error')]
    public function test_with_pair_param_fails_with_validation_error(
        array $data,
        string $path,
        string $messagePattern
    ): void {
        try {
            OptionalLongitudeFloatWithPairFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}
