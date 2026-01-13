<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\IsDigit;
use DataTypeTest\BaseTestCase;
use DataType\Exception\InvalidRulesExceptionData;

/**
 * @coversNothing
 */
class IsDigitTest extends BaseTestCase
{
    public function provideTestWorks()
    {
        yield ['0'];
        yield ['123'];
        yield ['0123456789'];
        yield ['999999'];
    }

    /**
     * @dataProvider provideTestWorks
     * @covers \DataType\ProcessRule\IsDigit
     */
    public function testWorks(string $testValue)
    {
        $rule = new IsDigit();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\IsDigit
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new IsDigit();
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
        yield ['abc', Messages::ERROR_NOT_DIGIT];
        yield ['123abc', Messages::ERROR_NOT_DIGIT];
        yield ['abc123', Messages::ERROR_NOT_DIGIT];
        yield ['12.34', Messages::ERROR_NOT_DIGIT];
        yield ['-123', Messages::ERROR_NOT_DIGIT];
        yield ['+123', Messages::ERROR_NOT_DIGIT];
        yield ['', Messages::ERROR_NOT_DIGIT];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\IsDigit
     * @param string $testValue
     * @param string $expected_error
     */
    public function testErrors(string $testValue, string $expected_error)
    {
        $rule = new IsDigit();
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
     * @covers \DataType\ProcessRule\IsDigit
     */
    public function testDescription()
    {
        $rule = new IsDigit();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame(ParamDescription::TYPE_INTEGER, $description->getType());
        /** @var \DataType\OpenApi\OpenApiV300ParamDescription $description */
        $this->assertSame('^[0-9]+$', $description->getPattern());
    }
}
