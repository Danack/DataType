<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetFloat;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

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

    public static function provideTestWorksCases()
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
     * @param int|string $input
     * @param float|int $expectedValue
     */
    #[DataProvider('provideTestWorksCases')]
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

    public static function provideTestErrorCases()
    {
        yield ['5.a'];
        yield ['banana'];
    }

    /**
     * @covers \DataType\ExtractRule\GetFloat
     * @param string $value
     */
    #[DataProvider('provideTestErrorCases')]
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
