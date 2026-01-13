<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\MatchesRegex;
use DataTypeTest\BaseTestCase;
use DataType\Exception\InvalidRulesExceptionData;

/**
 * @coversNothing
 */
class MatchesRegexTest extends BaseTestCase
{
    public function provideTestWorks()
    {
        yield ['^[a-z]+$', 'abc'];
        yield ['^[0-9]+$', '123'];
        yield ['^[a-zA-Z0-9]+$', 'abc123'];
        yield ['/^[a-z]+$/i', 'ABC']; // Case insensitive
        yield ['^test$', 'test'];
    }

    /**
     * @dataProvider provideTestWorks
     * @covers \DataType\ProcessRule\MatchesRegex
     */
    public function testWorks(string $pattern, string $testValue)
    {
        $rule = new MatchesRegex($pattern);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\MatchesRegex
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new MatchesRegex('^[0-9]+$');
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
        yield ['^[a-z]+$', 'ABC123', Messages::ERROR_PATTERN_MISMATCH];
        yield ['^[0-9]+$', 'abc', Messages::ERROR_PATTERN_MISMATCH];
        yield ['^test$', 'testing', Messages::ERROR_PATTERN_MISMATCH];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\MatchesRegex
     * @param string $pattern
     * @param string $testValue
     * @param string $expected_error
     */
    public function testErrors(string $pattern, string $testValue, string $expected_error)
    {
        $rule = new MatchesRegex($pattern);
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
     * @covers \DataType\ProcessRule\MatchesRegex
     */
    public function testDescription()
    {
        $rule = new MatchesRegex('^[a-z]+$');
        $description = $this->applyRuleToDescription($rule);
        /** @var \DataType\OpenApi\OpenApiV300ParamDescription $description */
        $this->assertSame('^[a-z]+$', $description->getPattern());
    }

    /**
     * @covers \DataType\ProcessRule\MatchesRegex
     */
    public function testDescriptionWithDelimiters()
    {
        $rule = new MatchesRegex('/^[a-z]+$/i');
        $description = $this->applyRuleToDescription($rule);
        /** @var \DataType\OpenApi\OpenApiV300ParamDescription $description */
        $this->assertSame('^[a-z]+$', $description->getPattern());
    }

    /**
     * @covers \DataType\ProcessRule\MatchesRegex
     */
    public function testWorksWithFlags()
    {
        $rule = new MatchesRegex('^[a-z]+$', 'i');
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 'ABC');
        $validationResult = $rule->process(
            'ABC', $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals('ABC', $validationResult->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\MatchesRegex
     */
    public function testWorksWithFlagsAndDelimiters()
    {
        $rule = new MatchesRegex('/^[a-z]+$/', 'i');
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 'ABC');
        $validationResult = $rule->process(
            'ABC', $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals('ABC', $validationResult->getValue());
    }
}
