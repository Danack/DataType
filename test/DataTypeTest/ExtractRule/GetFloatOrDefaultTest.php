<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetFloatOrDefault;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class GetFloatOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
//            // Test value is read as string
//            [new ArrayVarMap(['foo' => '5']), 'john', 5.0],

            // Test value is read as float
            [['foo' => 5], 20, 5.0],

//            // Test default is used as string
//            [new ArrayVarMap([]), '5', 5.0],

            // Test default is used as float
            [[], 5, 5.0],

            // Test default is used as null
            [[], null, null],

            // Extra checks
            [[], -1000.1, -1000.1],
        ];
    }

    /**
     * @covers \DataType\ExtractRule\GetFloatOrDefault
     * @dataProvider provideTestCases
     */
    public function testValidation($data, $default, $expectedValue)
    {
        $rule = new GetFloatOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromArray($data);
        $dataStorage = $dataStorage->moveKey('foo');

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        yield [null, Messages::FLOAT_REQUIRED_WRONG_TYPE];
        yield ['', Messages::NEED_FLOAT_NOT_EMPTY_STRING];
        yield ['6 apples', Messages::FLOAT_REQUIRED_FOUND_WHITESPACE];
        yield ['banana', Messages::FLOAT_REQUIRED];
        yield ['1.f', Messages::FLOAT_REQUIRED];
    }

    /**
     * @covers \DataType\ExtractRule\GetFloatOrDefault
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue, $message)
    {
        $default = 5.0;

        $validator = new ProcessedValues();
        $rule = new GetFloatOrDefault($default);
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetFloatOrDefault
     */
    public function testDescription()
    {
        $rule = new GetFloatOrDefault(4.5);
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('number', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertSame(4.5, $description->getDefault());
    }
}
