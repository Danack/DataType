<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Basic\OptionalBool;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\OptionalBoolDefaultTrueFixture;
use DataTypeTestFixture\Basic\OptionalBoolFixture;

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
     * @param class-string<OptionalBoolFixture|OptionalBoolDefaultTrueFixture> $fixtureClass
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_works_parses_input_to_expected')]
    public function test_works_parses_input_to_expected(string $fixtureClass, array $data, bool $expected): void
    {
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
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_fails_with_validation_error')]
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
