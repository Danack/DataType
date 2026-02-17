<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use DataType\Basic\OptionalInteger;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Basic\OptionalInteger
 */
class OptionalIntegerTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, int|null}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'integer' => [['count' => 42], 42];
        yield 'string integer' => [['count' => '99'], 99];
        yield 'missing gives null' => [[], null];
    }

    /**
     * @dataProvider provides_works_parses_input_to_expected
     * @param array<string, mixed> $data
     */
    public function test_works_parses_input_to_expected(array $data, int|null $expected): void
    {
        $result = OptionalIntegerFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'invalid type' => [['count' => 'not a number'], '/count', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
    }

    /**
     * @dataProvider provides_fails_with_validation_error
     * @param array<string, mixed> $data
     */
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            OptionalIntegerFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}

class OptionalIntegerFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalInteger('count')]
        public readonly int|null $value,
    ) {
    }
}
