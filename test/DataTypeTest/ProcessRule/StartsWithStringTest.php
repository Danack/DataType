<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\ProcessRule\MultipleEnum;
use DataType\ProcessRule\CheckOnlyAllowedCharacters;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\StartsWithString;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class StartsWithStringTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        return [
            ['pk_', 'pk_foobar'],
            ['_', '_foobar'],
        ];
    }

    /**
     * @dataProvider provideTestWorksCases
     * @covers \DataType\ProcessRule\StartsWithString
     */
    public function testValidationWorks(string $prefix, $testValue)
    {
        $rule = new StartsWithString($prefix);
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame($validationResult->getValue(), $testValue);
    }

    public function provideTestFailsCases()
    {
        return [
            ['pk_', 'dk_foobar'],
            ['_', 'f_oobar', true],
        ];
    }

    /**
     * @dataProvider provideTestFailsCases
     * @covers \DataType\ProcessRule\StartsWithString
     */
    public function testValidationErrors(string $prefix, $testValue)
    {
        $rule = new StartsWithString($prefix);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::STRING_REQUIRES_PREFIX,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\StartsWithString
     */
    public function testDescription()
    {
        $prefix = 'bar_';

        $rule = new StartsWithString($prefix);
        $description = $this->applyRuleToDescription($rule);
    }
}
