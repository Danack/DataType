<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ProcessedValues;
use DataType\ProcessRule\AlwaysErrorsRule;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class AlwaysErrorsRuleTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessRule\AlwaysErrorsRule
     */
    public function testWorks()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 'bar');
        $result = $rule->process(
            $unused_input = 5,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(1, $result->getValidationProblems());
        $this->assertValidationProblem(
            '/foo',
            $message,
            $result->getValidationProblems()
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\AlwaysErrorsRule
     */
    public function testCoverage()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);
        $description = $this->applyRuleToDescription($rule);
    }
}
