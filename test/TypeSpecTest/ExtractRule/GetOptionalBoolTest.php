<?php

declare(strict_types=1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ExtractRule\GetOptionalBool;
use TypeSpec\ProcessedValues;

/**
 * @coversNothing
 */
class GetOptionalBoolTest extends BaseTestCase
{
    public function provideTestCases()
    {
        yield from getBoolTestWorks();
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetOptionalBool
     * @dataProvider provideTestCases
     */
    public function testValidation($input, $expectedValue)
    {
        $rule = new GetOptionalBool();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }


    /**
     * @covers \TypeSpec\ExtractRule\GetOptionalBool
     */
    public function testMissingGivesNull()
    {
        $rule = new GetOptionalBool();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertNull($validationResult->getValue());
    }


    public function provideTestErrorCases()
    {
        yield [fopen('php://memory', 'r+'), Messages::UNSUPPORTED_TYPE]; // a stream is not a bool
        yield [[1, 2, 3], Messages::UNSUPPORTED_TYPE];  // an array is not a bool
        yield [new \StdClass(), Messages::UNSUPPORTED_TYPE]; // A stdClass is not a bool
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetOptionalBool
     * @dataProvider provideTestErrorCases
     */
    public function testBadInputErrors($inputValue, $message)
    {
        $validator = new ProcessedValues();
        $rule = new GetOptionalBool();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );


        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetOptionalBool
     */
    public function testDescription()
    {
        $rule = new GetOptionalBool();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('boolean', $description->getType());
        $this->assertFalse($description->getRequired());
    }
}
