<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\ValidUrl;
use DataType\ProcessedValues;
use DataType\Messages;

/**
 * @coversNothing
 */
class ValidUrlTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        yield ["https://www.google.com"];
    }

    /**
     * @dataProvider provideTestWorksCases
     * @covers \DataType\ProcessRule\ValidUrl
     */
    public function testValidationWorks($input)
    {
        $rule = new ValidUrl();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $input, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $input);
    }


    public function provideTestErrorsCases()
    {
        yield ['John', Messages::ERROR_INVALID_URL];
        yield ['https://www.', Messages::ERROR_INVALID_URL];
    }

    /**
     * @dataProvider provideTestErrorsCases
     * @covers \DataType\ProcessRule\ValidUrl
     */
    public function testValidationErrors($input, $expected_error)
    {
        $rule = new ValidUrl();
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
        $rule = new ValidUrl();
        $description = $this->applyRuleToDescription($rule);
    }
}
