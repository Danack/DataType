<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetFloatOrNull;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class GetFloatOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetFloatOrNull
     */
    public function testMissingGivesError()
    {
        $rule = new GetFloatOrNull();
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
            ['5', 5.0],
            ['5.5', 5.5],
            [5, 5.0],
            [5.5, 5.5],
            ['1000.1', 1000.1],
            ['-1000.1', -1000.1],
            [null, null]
        ];
    }

    /**
     * @covers \DataType\ExtractRule\GetFloatOrNull
     * @dataProvider provideTestWorksCases
     * @param int|float|string|null $input
     * @param float|null $expectedValue
     */
    public function testWorks($input, $expectedValue)
    {
        $validator = new ProcessedValues();
        $rule = new GetFloatOrNull();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input);

        $validationResult = $rule->process(
            $validator, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        yield ['5.a', Messages::FLOAT_REQUIRED];
        yield ['banana', Messages::FLOAT_REQUIRED];
    }

    /**
     * @covers \DataType\ExtractRule\GetFloatOrNull
     * @dataProvider provideTestErrorCases
     * @param string $input
     * @param string $message
     */
    public function testErrors($input, $message)
    {
        $rule = new GetFloatOrNull();
        $validator = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input);

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetFloatOrNull
     */
    public function testDescription()
    {
        $rule = new GetFloatOrNull();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('number', $description->getType());
        $this->assertTrue($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
