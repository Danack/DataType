<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use DataType\Basic\OptionalBool;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Basic\OptionalBool
 */
class OptionalBoolTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{string, array<string, mixed>, bool}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'true string' => [OptionalBoolFixture::class, ['flag' => 'true'], true];
        yield 'false string' => [OptionalBoolFixture::class, ['flag' => 'false'], false];
        yield 'missing gives default false' => [OptionalBoolFixture::class, [], false];
        yield 'missing gives default true' => [OptionalBoolDefaultTrueFixture::class, [], true];
    }

    /**
     * @dataProvider provides_works_parses_input_to_expected
     * @param class-string<DataType> $fixtureClass
     * @param array<string, mixed> $data
     */
    public function test_works_parses_input_to_expected(string $fixtureClass, array $data, bool $expected): void
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
        yield 'invalid type' => [['flag' => 123], '/flag', Messages::UNSUPPORTED_TYPE];
        yield 'bad string' => [['flag' => 'invalid'], '/flag', Messages::ERROR_BOOL_BAD_STRING];
    }

    /**
     * @dataProvider provides_fails_with_validation_error
     * @param array<string, mixed> $data
     */
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            OptionalBoolFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}

class OptionalBoolFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalBool('flag')]
        public readonly bool $value,
    ) {
    }
}

class OptionalBoolDefaultTrueFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalBool('flag', true)]
        public readonly bool $value,
    ) {
    }
}
