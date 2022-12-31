<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\ProcessedValues;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\AlwaysErrorsRule;
use TypeSpec\ProcessRule\AlwaysErrorsButDoesntHaltRule;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpec\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class AlwaysErrorsButDoesntHaltRuleTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ProcessRule\AlwaysErrorsRule
     */
    public function testWorks()
    {
        $message_always_errors = 'Always errors';
        $rule = new AlwaysErrorsRule($message_always_errors);
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
            $message_always_errors,
            $result->getValidationProblems()
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
    }

    /**
     * @covers \TypeSpec\ProcessRule\AlwaysErrorsRule
     */
    public function testCoverage()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);
        $description = $this->applyRuleToDescription($rule);
    }
}
