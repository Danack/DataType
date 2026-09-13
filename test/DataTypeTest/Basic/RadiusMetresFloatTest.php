<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Basic\RadiusMetresFloat;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\RadiusMetresFloatFixture;

/**
 * @covers \DataType\Basic\RadiusMetresFloat
 */
class RadiusMetresFloatTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'float' => [['radius_metres' => 1500.5], 1500.5];
        yield 'string float' => [['radius_metres' => '250'], 250.0];
        yield 'zero' => [['radius_metres' => 0.0], 0.0];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_works_parses_input_to_expected')]
    public function test_works_parses_input_to_expected(array $data, float $expected): void
    {
        $result = RadiusMetresFloatFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'missing' => [[], '/radius_metres', Messages::VALUE_NOT_SET];
        yield 'invalid type' => [['radius_metres' => 'not a number'], '/radius_metres', 'floating point number'];
        yield 'null' => [['radius_metres' => null], '/radius_metres', Messages::FLOAT_REQUIRED_WRONG_TYPE];
        yield 'negative' => [['radius_metres' => -0.1], '/radius_metres', Messages::FLOAT_TOO_SMALL];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_fails_with_validation_error')]
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            RadiusMetresFloatFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }

    public function test_get_input_type_returns_correct_type(): void
    {
        $propertyType = new RadiusMetresFloat('test_name');
        $inputType = $propertyType->getInputType();

        $this->assertInstanceOf(\DataType\InputType::class, $inputType);
        $this->assertSame('test_name', $inputType->getName());
    }
}
