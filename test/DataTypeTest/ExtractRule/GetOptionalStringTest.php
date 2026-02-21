<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetOptionalString;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetOptionalStringTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetOptionalString
     */
    public function testMissingGivesNull()
    {
        $rule = new GetOptionalString();
        $validator = new ProcessedValues();

        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );
        $this->assertNoProblems($validationResult);
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalString
     */
    public function testValidation()
    {
        $expectedValue = 'bar';

        $rule = new GetOptionalString();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalString
     */
    public function testBadTypeErrors()
    {
        $expectedValue = 'bar';

        $rule = new GetOptionalString();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 5)
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::STRING_EXPECTED]
        );
    }


    /**
     * @covers \DataType\ExtractRule\GetOptionalString
     */
    public function testDescription()
    {
        $rule = new GetOptionalString();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertFalse($description->getRequired());
    }
}
