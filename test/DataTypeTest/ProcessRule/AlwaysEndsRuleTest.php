<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\AlwaysEndsRule;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class AlwaysEndsRuleTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessRule\AlwaysEndsRule
     */
    public function testWorks()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $result = $rule->process(
            $unused_input = 4,
            $processedValues,
            $dataStorage
        );

        $this->assertNoProblems($result);
        $this->assertTrue($result->isFinalResult());
        $this->assertEquals($finalValue, $result->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\AlwaysEndsRule
     */
    public function testDescription()
    {
        $finalValue = 123;
        $rule = new AlwaysEndsRule($finalValue);
        $description = $this->applyRuleToDescription($rule);
        // nothing to assert.
    }
}
