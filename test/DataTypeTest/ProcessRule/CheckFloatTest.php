<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Exception\InvalidRulesExceptionData;
use DataType\Messages;
use DataType\ProcessRule\CheckFloat;
use DataTypeTest\BaseTestCase;

/**
 * @covers \DataType\ProcessRule\CheckFloat
 */
class CheckFloatTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{mixed, float}>
     */
    public static function provides_check_float_accepts_valid_input(): \Generator
    {
        yield 'float' => [1.5, 1.5];
//        yield 'int' => [3.0, 3.0];
//        yield 'string' => ['2.5', 2.5];
//        yield 'null' => [null, 0.0];
    }

    /**
     * @covers \DataType\ProcessRule\CheckFloat::checkFloat
     */
    #[DataProvider('provides_check_float_accepts_valid_input')]
    public function test_check_float_accepts_valid_input(mixed $input, float $expected): void
    {
        $obj = new class {
            use CheckFloat;
        };

        $this->assertSame($expected, $obj->checkFloat($input));
    }

    /**
     * @return \Generator<string, array{mixed}>
     */
    public static function provides_check_float_rejects_invalid_type(): \Generator
    {
        yield 'array' => [[]];
        yield 'object' => [new \stdClass()];
    }

    /**
     * @covers \DataType\ProcessRule\CheckFloat::checkFloat
     */
    #[DataProvider('provides_check_float_rejects_invalid_type')]
    public function test_check_float_rejects_invalid_type(mixed $invalidInput): void
    {
        $obj = new class {
            use CheckFloat;
        };

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(
            Messages::BAD_TYPE_FOR_FLOAT_PROCESS_RULE
        );
        $obj->checkFloat($invalidInput);
    }
}
