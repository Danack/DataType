<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\Exception\InvalidRulesExceptionData;
use DataType\Messages;
use DataType\ProcessRule\CheckInt;
use DataTypeTest\BaseTestCase;

/**
 * @covers \DataType\ProcessRule\CheckInt
 */
class CheckIntTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{mixed, int}>
     */
    public static function provides_check_int_accepts_valid_input(): \Generator
    {
        yield 'int' => [42, 42];
        yield 'string' => ['10', 10];
        yield 'null' => [null, 0];
    }

    /**
     * @dataProvider provides_check_int_accepts_valid_input
     * @covers \DataType\ProcessRule\CheckInt::checkInt
     */
    public function test_check_int_accepts_valid_input(mixed $input, int $expected): void
    {
        $obj = new class {
            use CheckInt;
        };

        $this->assertSame($expected, $obj->checkInt($input));
    }

    /**
     * @return \Generator<string, array{mixed}>
     */
    public static function provides_check_int_rejects_invalid_type(): \Generator
    {
        yield 'array' => [[]];
        yield 'object' => [new \stdClass()];
    }

    /**
     * @dataProvider provides_check_int_rejects_invalid_type
     * @covers \DataType\ProcessRule\CheckInt::checkInt
     */
    public function test_check_int_rejects_invalid_type(mixed $invalidInput): void
    {
        $obj = new class {
            use CheckInt;
        };

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(
            Messages::BAD_TYPE_FOR_INT_PROCESS_RULE
        );
        $obj->checkInt($invalidInput);
    }

    /**
     * @return \Generator<string, array{mixed, int|string}>
     */
    public static function provides_check_int_or_string_accepts_valid_input(): \Generator
    {
        yield 'int' => [7, 7];
        yield 'string' => ['123', '123'];
    }

    /**
     * @dataProvider provides_check_int_or_string_accepts_valid_input
     * @covers \DataType\ProcessRule\CheckInt::checkIntOrString
     */
    public function test_check_int_or_string_accepts_valid_input(mixed $input, int|string $expected): void
    {
        $obj = new class {
            use CheckInt;
        };

        $this->assertSame($expected, $obj->checkIntOrString($input));
    }

    /**
     * @return \Generator<string, array{mixed}>
     */
    public static function provides_check_int_or_string_rejects_invalid_type(): \Generator
    {
        yield 'float' => [1.5];
        yield 'array' => [[]];
        yield 'object' => [new \stdClass()];
    }

    /**
     * @dataProvider provides_check_int_or_string_rejects_invalid_type
     * @covers \DataType\ProcessRule\CheckInt::checkIntOrString
     */
    public function test_check_int_or_string_rejects_invalid_type(mixed $invalidInput): void
    {
        $obj = new class {
            use CheckInt;
        };

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(
            Messages::BAD_TYPE_FOR_INT_PROCESS_RULE
        );
        $obj->checkIntOrString($invalidInput);
    }
}
