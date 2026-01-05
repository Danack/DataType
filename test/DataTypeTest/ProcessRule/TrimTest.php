<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\Trim;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class TrimTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessRule\Trim
     */
    public function testValidation()
    {
        $rule = new Trim();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            ' bar ', $processedValues, TestArrayDataStorage::fromArraySetFirstValue([' bar '])
        );
        $this->assertNoProblems($validationResult);
        self::assertEquals($validationResult->getValue(), 'bar');
    }


    /**
     * @covers \DataType\ProcessRule\Trim
     */
    public function testDescription()
    {
        $rule = new Trim();
        $description = $this->applyRuleToDescription($rule);
        // nothing to test.
    }
}
