<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Basic\BasicIntegerOrDefault
 */
class BasicIntegerOrDefaultTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{string, array<string, mixed>, int|null}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'integer' => [BasicIntegerOrDefaultFixture::class, ['page' => 5], 5];
        yield 'missing gives default' => [BasicIntegerOrDefaultFixture::class, [], 1];
        yield 'missing gives null default' => [BasicIntegerOrDefaultNullFixture::class, [], null];
    }

    /**
     * @dataProvider provides_works_parses_input_to_expected
     * @param class-string<DataType> $fixtureClass
     * @param array<string, mixed> $data
     */
    public function test_works_parses_input_to_expected(string $fixtureClass, array $data, int|null $expected): void
    {
        /** @phpstan-ignore staticMethod.notFound (fixture classes use CreateFromVarMap trait) */
        $result = $fixtureClass::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'invalid type' => [['page' => 'not a number'], '/page', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
    }

    /**
     * @dataProvider provides_fails_with_validation_error
     * @param array<string, mixed> $data
     */
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            BasicIntegerOrDefaultFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}

class BasicIntegerOrDefaultFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicIntegerOrDefault('page', 1)]
        public readonly int|null $value,
    ) {
    }
}

class BasicIntegerOrDefaultNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicIntegerOrDefault('page', null)]
        public readonly int|null $value,
    ) {
    }
}
