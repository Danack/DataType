<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ProcessedValues;
use DataType\ProcessRule\AlwaysErrorsButDoesntHaltRule;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class AlwaysErrorsButDoesntHaltRuleTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessRule\AlwaysErrorsButDoesntHaltRule
     */
    public function testWorks()
    {
        $message_always_errors = 'Always errors';
        $rule = new AlwaysErrorsButDoesntHaltRule($message_always_errors);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 'bar');

        $input = 5;

        $result = $rule->process(
            $input,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(1, $result->getValidationProblems());
        $this->assertValidationProblem(
            '/foo',
            $message_always_errors,
            $result->getValidationProblems()
        );

        $this->assertFalse($result->isFinalResult());
        $this->assertSame($input, $result->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\AlwaysErrorsButDoesntHaltRule
     */
    public function testCoverage()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsButDoesntHaltRule($message);
        $this->applyRuleToDescription($rule);
    }
}
