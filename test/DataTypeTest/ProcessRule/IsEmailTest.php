<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\IsEmail;
use DataType\ProcessRule\IsRgbColor;
use DataTypeTest\BaseTestCase;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\Exception\InvalidRulesExceptionData;
use http\Message;

/**
 * @coversNothing
 */
class IsEmailTest extends BaseTestCase
{
    public function provideTestWorks()
    {
        yield ['john@example.com'];
        yield ['test@example.com'];
        yield ['"user@name"@example.com'];
        yield ['validipv6@[IPv6:2001:db8:1ff::a0b:dbd0]'];
        yield ['validipv4@[127.0.0.0]'];
    }

    /**
     * @dataProvider provideTestWorks
     * @covers \DataType\ProcessRule\IsEmail
     */
    public function testWorks(string $testValue)
    {
        $rule = new IsEmail();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\IsEmail
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new IsEmail();
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
        yield ['rgb', Messages::ERROR_EMAIL_NO_AT_CHARACTER];
        yield ['john at example.com', Messages::ERROR_EMAIL_NO_AT_CHARACTER];

        yield ['foo@@@@bar.com', Messages::ERROR_EMAIL_INVALID];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\IsEmail
     */
    public function testErrors($testValue, $expected_error)
    {
        $rule = new IsEmail();
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
     * @covers \DataType\ProcessRule\IsEmail
     */
    public function testDescription()
    {
        $rule = new IsEmail();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('email', $description->getFormat());
    }
}
