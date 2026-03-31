<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\ValidUrl;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class ValidUrlTest extends BaseTestCase
{
    public static function provideTestWorksCases()
    {
        yield ["https://www.google.com"];
        yield ["http://t.ly/"];
    }

    /**
     * @covers \DataType\ProcessRule\ValidUrl
     */
    #[DataProvider('provideTestWorksCases')]
    public function testValidationWorks(string $input)
    {
        $rule = new ValidUrl(true);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $input, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $input);
    }


    public static function provideTestErrorsCases()
    {
        yield ['John', Messages::ERROR_INVALID_URL];
        yield ['https://www.', Messages::ERROR_INVALID_URL];
        yield ["www.google.com", Messages::ERROR_INVALID_URL];
    }

    /**
     * @covers \DataType\ProcessRule\ValidUrl
     */
    #[DataProvider('provideTestErrorsCases')]
    public function testValidationErrors(string $input, string $expected_error)
    {
        $rule = new ValidUrl(true);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $input,
            $processedValues,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $expected_error,
            $validationResult->getValidationProblems()
        );
    }


    public static function provideTestWorksCases_scheme_optional()
    {
        yield ["https://www.google.com"];
        yield ["http://t.ly/"];
        yield ["www.google.com"];
        yield ["t.ly/"];
        yield ["https://opencouncil.network/"];
    }

    /**
     * @covers \DataType\ProcessRule\ValidUrl
     */
    #[DataProvider('provideTestWorksCases_scheme_optional')]
    public function testValidationWorks_scheme_optional(string $input)
    {
        $rule = new ValidUrl(false);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $input, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $input);
    }


    public static function provideTestErrorsCases_scheme_optional()
    {
        yield ['John', Messages::ERROR_INVALID_URL];
        yield ['https://www.', Messages::ERROR_INVALID_URL];
    }

    /**
     * @covers \DataType\ProcessRule\ValidUrl
     */
    #[DataProvider('provideTestErrorsCases_scheme_optional')]
    public function testValidationErrors_scheme_optional(string $input, string $expected_error)
    {
        $rule = new ValidUrl(true);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $input,
            $processedValues,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $expected_error,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\ValidUrl
     */
    public function testDescription()
    {
        $rule = new ValidUrl(true);
        $description = $this->applyRuleToDescription($rule);
    }
}
