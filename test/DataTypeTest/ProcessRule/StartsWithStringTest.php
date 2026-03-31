<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\StartsWithString;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class StartsWithStringTest extends BaseTestCase
{
    public static function provideTestWorksCases()
    {
        return [
            ['pk_', 'pk_foobar'],
            ['_', '_foobar'],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\StartsWithString
     */
    #[DataProvider('provideTestWorksCases')]
    public function testValidationWorks(string $prefix, string $testValue)
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

    public static function provideTestFailsCases()
    {
        return [
            ['pk_', 'dk_foobar'],
            ['_', 'f_oobar'],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\StartsWithString
     */
    #[DataProvider('provideTestFailsCases')]
    public function testValidationErrors(string $prefix, string $testValue)
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
