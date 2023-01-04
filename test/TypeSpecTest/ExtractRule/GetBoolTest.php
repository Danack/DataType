<?php

declare(strict_types=1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ExtractRule\GetBool;
use TypeSpec\ProcessedValues;

/**
 * @coversNothing
 */
class GetBoolTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ExtractRule\GetBool
     */
    public function testMissingGivesError()
    {
        $rule = new GetBool();
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
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetBool
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {
        $validator = new ProcessedValues();
        $rule = new GetBool();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            // todo - we should test the exact error.
            [fopen('php://memory', 'r+')],
            [[1, 2, 3]],
            [new \StdClass()]
        ];
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetBool
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($value)
    {
        $rule = new GetBool();
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
     * @covers \TypeSpec\ExtractRule\GetBool
     * @dataProvider provideTestErrorCasesForBadStrings
     */
    public function testErrorsWithBadStrings($value)
    {
        $rule = new GetBool();
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
     * @covers \TypeSpec\ExtractRule\GetBool
     */
    public function testDescription()
    {
        $rule = new GetBool();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('boolean', $description->getType());
        $this->assertTrue($description->getRequired());
    }
}
