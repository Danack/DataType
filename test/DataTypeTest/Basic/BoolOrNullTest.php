<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use DataType\Basic\BoolOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Basic\BoolOrNull
 */
class BoolOrNullTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, bool|null}>
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'true' => [['flag' => true], true];
        yield 'false' => [['flag' => false], false];
        yield 'null value' => [['flag' => null], null];
    }

    /**
     * @dataProvider provides_works_parses_input_to_expected
     * @param array<string, mixed> $data
     */
    public function test_works_parses_input_to_expected(array $data, bool|null $expected): void
    {
        $result = BoolOrNullFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($expected, $result->value);
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'missing parameter' => [[], '/flag', Messages::VALUE_NOT_SET];
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
            BoolOrNullFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}

class BoolOrNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BoolOrNull('flag')]
        public readonly bool|null $value,
    ) {
    }
}
