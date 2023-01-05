<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\ProcessRule\CastToBool;
use DataTypeTest\BaseTestCase;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class CastToBoolTest extends BaseTestCase
{
    public function provideBoolValueWorksCases()
    {
        yield from getBoolTestWorks();
    }

    /**
     * @dataProvider provideBoolValueWorksCases
     * @covers \DataType\ProcessRule\CastToBool
     */
    public function testValidationWorks($inputValue, bool $expectedValue)
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

    public function provideBoolValueErrorsCases()
    {
        yield [fopen('php://memory', 'r+'), Messages::UNSUPPORTED_TYPE];
        yield [[1, 2, 3], Messages::UNSUPPORTED_TYPE];
        yield [new \StdClass(), Messages::UNSUPPORTED_TYPE];
        yield ["John", Messages::ERROR_BOOL_BAD_STRING];
    }

    /**
     * @dataProvider provideBoolValueErrorsCases
     * @covers \DataType\ProcessRule\CastToBool
     */
    public function testValidationErrors($inputValue, $message)
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
