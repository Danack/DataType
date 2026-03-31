<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\CastToBool;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class CastToBoolTest extends BaseTestCase
{
    public static function provideBoolValueWorksCases()
    {
        yield from getBoolTestWorks();
    }

    /**
     * @covers \DataType\ProcessRule\CastToBool
     */
    #[DataProvider('provideBoolValueWorksCases')]
    public function testValidationWorks(string|bool $inputValue, bool $expectedValue)
    {
        $rule = new CastToBool();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public static function provideBoolValueErrorsCases()
    {
        yield [fopen('php://memory', 'r+'), Messages::UNSUPPORTED_TYPE];
        yield [[1, 2, 3], Messages::UNSUPPORTED_TYPE];
        yield [new \stdClass(), Messages::UNSUPPORTED_TYPE];
        yield ["John", Messages::ERROR_BOOL_BAD_STRING];
    }

    /**
     * @covers \DataType\ProcessRule\CastToBool
     */
    #[DataProvider('provideBoolValueErrorsCases')]
    public function testValidationErrors(mixed $inputValue, string $message)
    {
        $rule = new CastToBool();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', [$inputValue])
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\CastToBool
     */
    public function testDescription()
    {
        $rule = new CastToBool();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('boolean', $description->getType());
    }
}
