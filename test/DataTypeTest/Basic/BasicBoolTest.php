<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Basic\BasicBool;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\BasicBoolFixture;

/**
 * @covers \DataType\Basic\BasicBool
 */
class BasicBoolTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, bool}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'true bool' => [['flag' => true], true];
        yield 'false bool' => [['flag' => false], false];
        yield 'true string' => [['flag' => 'true'], true];
        yield 'false string' => [['flag' => 'false'], false];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_works_parses_input_to_expected')]
    public function test_works_parses_input_to_expected(array $data, bool $expected): void
    {
        $result = BasicBoolFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'missing required' => [[], '/flag', Messages::VALUE_NOT_SET];
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
            BasicBoolFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}
