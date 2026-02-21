<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use DataType\Basic\FloatOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Basic\FloatOrDefault
 */
class FloatOrDefaultTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{string, array<string, mixed>, float|null}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'float' => [FloatOrDefaultFixture::class, ['rate' => 2.5], 2.5];
        yield 'missing gives default' => [FloatOrDefaultFixture::class, [], 1.0];
        yield 'missing gives null default' => [FloatOrDefaultNullFixture::class, [], null];
    }

    /**
     * @dataProvider provides_works_parses_input_to_expected
     * @param class-string<FloatOrDefaultFixture|FloatOrDefaultNullFixture> $fixtureClass
     * @param array<string, mixed> $data
     */
    public function test_works_parses_input_to_expected(string $fixtureClass, array $data, float|null $expected): void
    {
        $result = $fixtureClass::createFromVarMap(new ArrayVarMap($data));
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
            FloatOrDefaultFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}

class FloatOrDefaultFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[FloatOrDefault('rate', 1.0)]
        public readonly float|null $value,
    ) {
    }
}

class FloatOrDefaultNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[FloatOrDefault('rate', null)]
        public readonly float|null $value,
    ) {
    }
}
