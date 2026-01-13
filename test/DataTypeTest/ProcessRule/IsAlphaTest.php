<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\IsAlpha;
use DataTypeTest\BaseTestCase;
use DataType\Exception\InvalidRulesExceptionData;

/**
 * @coversNothing
 */
class IsAlphaTest extends BaseTestCase
{
    public function provideTestWorks()
    {
        yield ['abc'];
        yield ['ABC'];
        yield ['AbC'];
        yield ['abcdefghijklmnopqrstuvwxyz'];
        yield ['ABCDEFGHIJKLMNOPQRSTUVWXYZ'];
    }

    /**
     * @dataProvider provideTestWorks
     * @covers \DataType\ProcessRule\IsAlpha
     */
    public function testWorks(string $testValue)
    {
        $rule = new IsAlpha();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\IsAlpha
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new IsAlpha();
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
        yield ['abc123', Messages::ERROR_NOT_ALPHA];
        yield ['123', Messages::ERROR_NOT_ALPHA];
        yield ['abc def', Messages::ERROR_NOT_ALPHA];
        yield ['abc-def', Messages::ERROR_NOT_ALPHA];
        yield ['', Messages::ERROR_NOT_ALPHA];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\IsAlpha
     * @param string $testValue
     * @param string $expected_error
     */
    public function testErrors(string $testValue, string $expected_error)
    {
        $rule = new IsAlpha();
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
     * @covers \DataType\ProcessRule\IsAlpha
     */
    public function testDescription()
    {
        $rule = new IsAlpha();
        $description = $this->applyRuleToDescription($rule);
        /** @var \DataType\OpenApi\OpenApiV300ParamDescription $description */
        $this->assertSame('^[a-zA-Z]+$', $description->getPattern());
    }
}
