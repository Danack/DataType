<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use VarMap\ArrayVarMap;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\ProcessedValues;
use DataType\Messages;

/**
 * @coversNothing
 */
class GetStringOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            [new ArrayVarMap(['foo' => 'bar']), 'john', 'bar'],
            [new ArrayVarMap([]), 'john', 'john'],
        ];
    }

    /**
     * @covers \DataType\ExtractRule\GetStringOrDefault
     */
    public function testValidation()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::fromArraySetFirstValue(['John'])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), 'John');
    }

    /**
     * @covers \DataType\ExtractRule\GetStringOrDefault
     */
    public function testValidationForMissing()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $default);
    }

    /**
     * @covers \DataType\ExtractRule\GetStringOrDefault
     */
    public function testGetDefault()
    {
        $default = 'bar';
        $rule = new GetStringOrDefault($default);
        $this->assertSame($default, $rule->getDefault());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalString
     */
    public function testBadTypeErrors()
    {
        $rule = new GetStringOrDefault('bar');
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
     * @covers \DataType\ExtractRule\GetStringOrDefault
     */
    public function testDescription()
    {
        $rule = new GetStringOrDefault('John');
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertSame('John', $description->getDefault());
    }
}
