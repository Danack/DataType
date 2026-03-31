<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Basic\StringOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\StringOrDefaultFixture;
use DataTypeTestFixture\Basic\StringOrDefaultNullFixture;

/**
 * @covers \DataType\Basic\StringOrDefault
 */
class StringOrDefaultTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{string, array<string, mixed>, string|null}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'string' => [StringOrDefaultFixture::class, ['sort' => 'name'], 'name'];
        yield 'missing gives default' => [StringOrDefaultFixture::class, [], 'date'];
        yield 'missing gives null default' => [StringOrDefaultNullFixture::class, [], null];
    }

    /**
     * @param class-string<StringOrDefaultFixture|StringOrDefaultNullFixture> $fixtureClass
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_works_parses_input_to_expected')]
    public function test_works_parses_input_to_expected(string $fixtureClass, array $data, string|null $expected): void
    {
        $result = $fixtureClass::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'invalid type' => [['sort' => 123], '/sort', Messages::STRING_EXPECTED];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_fails_with_validation_error')]
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            StringOrDefaultFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}
