<?php

declare(strict_types = 1);

namespace DataTypeTest\ExtractRule;

use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\FixedValue;
use DataType\ProcessedValues;
use DataType\DataStorage\ArrayDataStorage;

/**
 * @coversNothing
 */
class FixedValueTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\FixedValue
     */
    public function testMissingGivesError()
    {
        $value = 4;
        $rule = new FixedValue($value);

        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $processedValues,
            ArrayDataStorage::fromArray([])
        );

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->anyErrorsFound());
    }

    /**
     * @covers \DataType\ExtractRule\FixedValue
     */
    public function test_coverage()
    {
        $value = 4;
        $rule = new FixedValue($value);

        $description = $this->applyRuleToDescription($rule);
    }
}
