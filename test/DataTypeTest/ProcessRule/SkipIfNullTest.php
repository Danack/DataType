<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ProcessedValues;
use DataType\ProcessRule\SkipIfNull;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class SkipIfNullTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            [null, true],
            [1, false],
            [0, false],
            [[], false],
            ['banana', false],

        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \DataType\ProcessRule\SkipIfNull
     * @param array<int, mixed>|int|string|null $testValue
     */
    public function testValidation($testValue, bool $expectIsFinalResult)
    {
        $rule = new SkipIfNull();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );
        $this->assertEquals($validationResult->isFinalResult(), $expectIsFinalResult);
    }

    /**
     * @covers \DataType\ProcessRule\SkipIfNull
     */
    public function testDescription()
    {
        $rule = new SkipIfNull();
        $description = $this->applyRuleToDescription($rule);
    }
}
