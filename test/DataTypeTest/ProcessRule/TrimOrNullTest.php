<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ProcessedValues;
use DataType\ProcessRule\TrimOrNull;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class TrimOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessRule\TrimOrNull
     */
    public function testValidation()
    {
        $rule = new TrimOrNull();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            ' bar ', $processedValues, TestArrayDataStorage::fromArraySetFirstValue([' bar '])
        );
        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), 'bar');
    }

    /**
     * @covers \DataType\ProcessRule\TrimOrNull
     */
    public function testValidation_for_null()
    {
        $rule = new TrimOrNull();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            null, $processedValues, TestArrayDataStorage::fromArraySetFirstValue([null])
        );
        $this->assertNoProblems($validationResult);
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\TrimOrNull
     */
    public function testDescription()
    {
        $rule = new TrimOrNull();
        $description = $this->applyRuleToDescription($rule);
        // nothing to test.
    }
}
