<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use DataType\Basic\OptionalFloat;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Basic\OptionalFloat
 */
class OptionalFloatTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, float|null}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'float' => [['rate' => 3.14], 3.14];
        yield 'string float' => [['rate' => '2.5'], 2.5];
        yield 'missing gives null' => [[], null];
    }

    /**
     * @dataProvider provides_works_parses_input_to_expected
     * @param array<string, mixed> $data
     */
    public function test_works_parses_input_to_expected(array $data, float|null $expected): void
    {
        $result = OptionalFloatFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'invalid type' => [['rate' => 'not a number'], '/rate', 'floating point number'];
    }

    /**
     * @dataProvider provides_fails_with_validation_error
     * @param array<string, mixed> $data
     */
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            OptionalFloatFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}

class OptionalFloatFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalFloat('rate')]
        public readonly float|null $value,
    ) {
    }
}
