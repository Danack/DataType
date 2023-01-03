<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\ProcessedValues;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\AlwaysErrorsButDoesntHaltRule;
use TypeSpec\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class AlwaysErrorsButDoesntHaltRuleTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ProcessRule\AlwaysErrorsButDoesntHaltRule
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
     * @covers \TypeSpec\ProcessRule\AlwaysErrorsButDoesntHaltRule
     */
    public function testCoverage()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsButDoesntHaltRule($message);
        $this->applyRuleToDescription($rule);
    }
}
