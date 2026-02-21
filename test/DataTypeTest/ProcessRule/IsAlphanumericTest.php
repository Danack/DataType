<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Exception\InvalidRulesExceptionData;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\IsAlphanumeric;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class IsAlphanumericTest extends BaseTestCase
{
    public function provideTestWorks()
    {
        yield ['abc'];
        yield ['ABC'];
        yield ['123'];
        yield ['abc123'];
        yield ['ABC123'];
        yield ['AbC123'];
        yield ['abcdefghijklmnopqrstuvwxyz0123456789'];
    }

    /**
     * @dataProvider provideTestWorks
     * @covers \DataType\ProcessRule\IsAlphanumeric
     */
    public function testWorks(string $testValue)
    {
        $rule = new IsAlphanumeric();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\IsAlphanumeric
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new IsAlphanumeric();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(
            \DataType\Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );

        $rule->process(
            $testValue, $processedValues, $dataStorage
        );
    }

    public function provideTestErrors()
    {
        yield ['abc def', Messages::ERROR_NOT_ALPHANUMERIC];
        yield ['abc-def', Messages::ERROR_NOT_ALPHANUMERIC];
        yield ['abc.def', Messages::ERROR_NOT_ALPHANUMERIC];
        yield ['abc@def', Messages::ERROR_NOT_ALPHANUMERIC];
        yield ['', Messages::ERROR_NOT_ALPHANUMERIC];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\IsAlphanumeric
     * @param string $testValue
     * @param string $expected_error
     */
    public function testErrors(string $testValue, string $expected_error)
    {
        $rule = new IsAlphanumeric();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertTrue($validationResult->anyErrorsFound());

        $this->assertValidationProblemRegexp(
            '/foo',
            $expected_error,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\IsAlphanumeric
     */
    public function testDescription()
    {
        $rule = new IsAlphanumeric();
        $description = $this->applyRuleToDescription($rule);
        /** @var \DataType\OpenApi\OpenApiV300ParamDescription $description */
        $this->assertSame('^[a-zA-Z0-9]+$', $description->getPattern());
    }
}
