<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use DataType\Basic\OptionalLongitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

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
     * @dataProvider provides_works_parses_input_to_expected
     * @param array<string, mixed> $data
     */
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
     * @dataProvider provides_fails_with_validation_error
     * @param array<string, mixed> $data
     */
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            OptionalLongitudeFloatFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
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
}

class OptionalLongitudeFloatFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalLongitudeFloat('lng')]
        public readonly float|null $value,
    ) {
    }
}
