<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Basic\BasicIntegerOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\BasicIntegerOrDefaultFixture;
use DataTypeTestFixture\Basic\BasicIntegerOrDefaultNullFixture;

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
     * @param class-string<BasicIntegerOrDefaultFixture|BasicIntegerOrDefaultNullFixture> $fixtureClass
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_works_parses_input_to_expected')]
    public function test_works_parses_input_to_expected(string $fixtureClass, array $data, int|null $expected): void
    {
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
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_fails_with_validation_error')]
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
