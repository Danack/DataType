<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\StartsWithString;
use Params\ProcessedValues;

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
     * @covers \Params\ProcessRule\StartsWithString
     */
    public function testValidationWorks(string $prefix, $testValue)
    {
        $rule = new StartsWithString($prefix);
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
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
     * @covers \Params\ProcessRule\StartsWithString
     */
    public function testValidationErrors(string $prefix, $testValue)
    {
        $rule = new StartsWithString($prefix);
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromSingleValue('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::STRING_MUST_START_WITH,
            $validationResult->getValidationProblems()
        );
    }
}
