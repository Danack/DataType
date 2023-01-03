<?php

declare(strict_types=1);

namespace TypeSpecTest;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\DataType;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\AlwaysEndsRule;
use TypeSpec\DataStorage\TestArrayDataStorage;
use function TypeSpec\processDataTypeWithDataStorage;

/**
 * @coversNothing
 */
class ParamsValidatorTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ProcessedValues
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $param = new DataType(
            'foo',
            new GetInt(),
            // This rule will stop processing
            new AlwaysEndsRule($finalValue),
            // this rule would give an error if processing was not stopped.
            new MaxIntValue($finalValue - 5)
        );

        $processedValues = new ProcessedValues();

        $errors = processDataTypeWithDataStorage(
            $param,
            $processedValues,
            TestArrayDataStorage::fromArray(['foo' => 5])
        );

        $this->assertNoValidationProblems($errors);

        $this->assertTrue($processedValues->hasValue('foo'));
        $value = $processedValues->getValue('foo');

        $this->assertEquals($finalValue, $value);
    }
}
