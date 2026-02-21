<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use DataType\Basic\LatitudeFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Basic\LatitudeFloat
 */
class LatitudeFloatTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'float' => [['lat' => 51.4545], 51.4545];
        yield 'string float' => [['lat' => '51.4545'], 51.4545];
        yield 'boundary min' => [['lat' => -90.0], -90.0];
        yield 'boundary max' => [['lat' => 90.0], 90.0];
        yield 'zero' => [['lat' => 0.0], 0.0];
    }

    /**
     * @dataProvider provides_works_parses_input_to_expected
     * @param array<string, mixed> $data
     */
    public function test_works_parses_input_to_expected(array $data, float $expected): void
    {
        $result = LatitudeFloatFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'missing' => [[], '/lat', Messages::VALUE_NOT_SET];
        yield 'invalid type' => [['lat' => 'not a number'], '/lat', 'floating point number'];
        yield 'null' => [['lat' => null], '/lat', Messages::FLOAT_REQUIRED_WRONG_TYPE];
        yield 'too small' => [['lat' => -90.1], '/lat', Messages::FLOAT_TOO_SMALL];
        yield 'too large' => [['lat' => 90.1], '/lat', Messages::FLOAT_TOO_LARGE];
    }

    /**
     * @dataProvider provides_fails_with_validation_error
     * @param array<string, mixed> $data
     */
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            LatitudeFloatFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        }
        catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }

    public function test_implements_has_input_type(): void
    {
        $propertyType = new LatitudeFloat('test_name');
        $this->assertInstanceOf(\DataType\HasInputType::class, $propertyType);
    }

    public function test_get_input_type_returns_correct_type(): void
    {
        $propertyType = new LatitudeFloat('test_name');
        $inputType = $propertyType->getInputType();

        $this->assertInstanceOf(\DataType\InputType::class, $inputType);
        $this->assertSame('test_name', $inputType->getName());
    }
}

class LatitudeFloatFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[LatitudeFloat('lat')]
        public readonly float $value,
    ) {
    }
}
