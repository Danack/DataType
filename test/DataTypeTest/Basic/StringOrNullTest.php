<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Basic\StringOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\Exception\Runtime\ValidationException;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\StringOrNullFixture;
use function DataType\createSingleValue;

/**
 * @covers \DataType\Basic\StringOrNull
 */
class StringOrNullTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, string|null}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'string' => [['name' => 'hello'], 'hello'];
        yield 'null value' => [['name' => null], null];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_works_parses_input_to_expected')]
    public function test_works_parses_input_to_expected(array $data, string|null $expected): void
    {
        $result = StringOrNullFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'missing parameter' => [[], '/name', Messages::VALUE_NOT_SET];
        yield 'invalid type' => [['name' => 123], '/name', Messages::STRING_EXPECTED];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_fails_with_validation_error')]
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            StringOrNullFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\Runtime\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }

    public function testFailsWhenLongerThanMaxLength(): void
    {
        $propertyType = new StringOrNull('name', 5);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::STRING_TOO_LONG);
        createSingleValue($propertyType, 'abcdef');
    }

    public function testWorksAtMaxLength(): void
    {
        $propertyType = new StringOrNull('name', 5);
        $result = createSingleValue($propertyType, 'abcde');
        $this->assertSame('abcde', $result);
    }
}
