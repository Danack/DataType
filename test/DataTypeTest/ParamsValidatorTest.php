<?php

declare(strict_types=1);

namespace DataTypeTest;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetInt;
use DataType\InputType;
use DataType\ProcessedValues;
use DataType\ProcessRule\AlwaysEndsRule;
use DataType\ProcessRule\MaxIntValue;
use function DataType\processInputTypeWithDataStorage;

/**
 * @coversNothing
 */
class ParamsValidatorTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessedValues
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $param = new InputType(
            'foo',
            new GetInt(),
            // This rule will stop processing
            new AlwaysEndsRule($finalValue),
            // this rule would give an error if processing was not stopped.
            new MaxIntValue($finalValue - 5)
        );

        $processedValues = new ProcessedValues();

        $errors = processInputTypeWithDataStorage(
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
