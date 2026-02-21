<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetBoolOrNull;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetBoolOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetBoolOrNull
     */
    public function testMissingGivesError()
    {
        $rule = new GetBoolOrNull();
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
        yield from getBoolTestWorks();
        yield [null, null];
    }

    /**
     * @covers \DataType\ExtractRule\GetBoolOrNull
     * @dataProvider provideTestWorksCases
     * @param bool|string|null $input
     * @param bool|null $expectedValue
     */
    public function testWorks(bool|string|null $input, ?bool $expectedValue)
    {
        $validator = new ProcessedValues();
        $rule = new GetBoolOrNull();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input);

        $validationResult = $rule->process(
            $validator, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            [fopen('php://memory', 'r+')],
            [[1, 2, 3]],
            [new \stdClass()]
        ];
    }

    /**
     * @covers \DataType\ExtractRule\GetBoolOrNull
     * @dataProvider provideTestErrorCases
     * @param mixed $value
     */
    public function testErrors($value)
    {
        $rule = new GetBoolOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue(['foo' => $value])
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::UNSUPPORTED_TYPE,
            $validationResult->getValidationProblems()
        );
    }

    public function provideTestErrorCasesForBadStrings()
    {
        yield from getBoolBadStrings();
    }

    /**
     * @covers \DataType\ExtractRule\GetBoolOrNull
     * @dataProvider provideTestErrorCasesForBadStrings
     * @param string $value
     */
    public function testErrorsWithBadStrings(string $value)
    {
        $rule = new GetBoolOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue(['foo' => $value])
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_BOOL_BAD_STRING,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetBoolOrNull
     */
    public function testDescription()
    {
        $rule = new GetBoolOrNull();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('boolean', $description->getType());
        $this->assertTrue($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
