<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetOptionalBool;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetOptionalBoolTest extends BaseTestCase
{
    public static function provideTestCases()
    {
        yield from getBoolTestWorks();
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalBool
     * @param mixed $input
     * @param bool $expectedValue
     */
    #[DataProvider('provideTestCases')]
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
     * @covers \DataType\ExtractRule\GetOptionalBool
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


    public static function provideTestErrorCases()
    {
        yield [fopen('php://memory', 'r+'), Messages::UNSUPPORTED_TYPE]; // a stream is not a bool
        yield [[1, 2, 3], Messages::UNSUPPORTED_TYPE];  // an array is not a bool
        yield [new \stdClass(), Messages::UNSUPPORTED_TYPE]; // A stdClass is not a bool
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalBool
     * @param mixed $inputValue
     * @param string $message
     */
    #[DataProvider('provideTestErrorCases')]
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
     * @covers \DataType\ExtractRule\GetOptionalBool
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
