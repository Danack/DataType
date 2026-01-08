<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetFloat;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class GetFloatTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetFloat
     */
    public function testMissingGivesError()
    {
        $rule = new GetFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::VALUE_NOT_SET]
        );
    }

    public function provideTestWorksCases()
    {
        return [
            ['5', 5],
            ['555555', 555555],
            ['1000.1', 1000.1],
            ['-1000.1', -1000.1],
        ];
    }

    /**
     * @covers \DataType\ExtractRule\GetFloat
     * @dataProvider provideTestWorksCases
     * @param int|string $input
     * @param float|int $expectedValue
     */
    public function testWorks($input, $expectedValue)
    {
        $variableName = 'foo';
        $validator = new ProcessedValues();
        $rule = new GetFloat();
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::fromArraySetFirstValue([$variableName => $input])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        yield ['5.a'];
        yield ['banana'];
    }

    /**
     * @covers \DataType\ExtractRule\GetFloat
     * @dataProvider provideTestErrorCases
     * @param string $value
     */
    public function testErrors($value)
    {
        $rule = new GetFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $value)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::FLOAT_REQUIRED,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetFloat
     */
    public function testDescription()
    {
        $rule = new GetFloat();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('number', $description->getType());
        $this->assertTrue($description->getRequired());
    }
}
