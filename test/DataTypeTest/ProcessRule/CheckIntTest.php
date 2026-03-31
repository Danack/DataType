<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
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
     * @return \Generator<string, array{int, int}>
     */
    public static function provides_check_int_accepts_valid_input(): \Generator
    {
        yield 'int' => [42, 42];
    }

    /**
     * @covers \DataType\ProcessRule\CheckInt::checkInt
     */
    #[DataProvider('provides_check_int_accepts_valid_input')]
    public function test_check_int_accepts_valid_input(int $input, int $expected): void
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
        yield 'string' => ['10'];
        yield 'null' => [null];
        yield 'float' => [1.5];
        yield 'array' => [[]];
        yield 'object' => [new \stdClass()];
    }

    /**
     * @covers \DataType\ProcessRule\CheckInt::checkInt
     */
    #[DataProvider('provides_check_int_rejects_invalid_type')]
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
}
