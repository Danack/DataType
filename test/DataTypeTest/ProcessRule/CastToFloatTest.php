<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\ProcessRule\CastToFloat;
use DataTypeTest\BaseTestCase;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class CastToFloatTest extends BaseTestCase
{
    public function provideWorksCases()
    {
        return [
            [5.0, 5.0],
            ['5', 5],
            ['555555', 555555],
            ['1000.1', 1000.1],
            ['-1000.1', -1000.1],
        ];
    }

    /**
     * @dataProvider provideWorksCases
     * @covers \DataType\ProcessRule\CastToFloat
     */
    public function testValidationWorks(float|string $inputValue, float $expectedValue)
    {
        $rule = new CastToFloat();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideErrorCases()
    {
        return [
            [[], Messages::FLOAT_REQUIRED_WRONG_TYPE],
            [null, Messages::FLOAT_REQUIRED_WRONG_TYPE],
            ['', Messages::NEED_FLOAT_NOT_EMPTY_STRING],
            ['5.a', Messages::FLOAT_REQUIRED],
            ['5. 5', Messages::FLOAT_REQUIRED_FOUND_WHITESPACE], // space in middle
            ['5.5 ', Messages::FLOAT_REQUIRED_FOUND_WHITESPACE], // trailing space
            [' 5.5', Messages::FLOAT_REQUIRED_FOUND_WHITESPACE], // leading space
            ['5.5banana', Messages::FLOAT_REQUIRED], // trailing invalid chars
            ['banana', Messages::FLOAT_REQUIRED],
        ];
    }

    /**
     * @dataProvider provideErrorCases
     * @covers \DataType\ProcessRule\CastToFloat
     */
    public function testValidationErrors(string|array|null $inputValue, $message)
    {
        $rule = new CastToFloat();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\CastToFloat
     */
    public function testDescription()
    {
        $rule = new CastToFloat();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('number', $description->getType());
    }
}
