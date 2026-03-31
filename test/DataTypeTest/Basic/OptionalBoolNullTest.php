<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Basic\OptionalBoolNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\OptionalBoolNullFixture;

/**
 * @covers \DataType\Basic\OptionalBoolNull
 */
class OptionalBoolNullTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, bool|null}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'true string' => [['flag' => 'true'], true];
        yield 'false string' => [['flag' => 'false'], false];
        yield 'missing gives null' => [[], null];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_works_parses_input_to_expected')]
    public function test_works_parses_input_to_expected(array $data, bool|null $expected): void
    {
        $result = OptionalBoolNullFixture::createFromVarMap(new ArrayVarMap($data));
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
            OptionalBoolNullFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}
