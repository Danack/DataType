<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetBoolOrDefault;
use DataType\ProcessedValues;
use DataType\Messages;

/**
 * @coversNothing
 */
class GetBoolOrDefaultTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetBoolOrDefault
     */
    public function testMissingCorrect()
    {
        $defaults = [true, false];

        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorage = $dataStorage->moveKey('foo');

        foreach ($defaults as $default) {
            $rule = new GetBoolOrDefault($default);
            $validator = new ProcessedValues();
            $validationResult = $rule->process(
                $validator,
                $dataStorage
            );
            $this->assertNoProblems($validationResult);
            $this->assertSame($default, $validationResult->getValue());
        }
    }

    public function provideTestWorksCases()
    {
        yield from getBoolTestWorks();
    }

    /**
     * @covers \DataType\ExtractRule\GetBoolOrDefault
     * @dataProvider provideTestWorksCases
     */
    public function testWorks(string|bool $input, bool $expectedValue)
    {
        $validator = new ProcessedValues();
        $rule = new GetBoolOrDefault(false);
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCasesBadTypes()
    {
        // todo - we should test the exact error.
        yield [fopen('php://memory', 'r+')];
        yield [[1, 2, 3]];
        yield [new \stdClass()];
        yield [null];
        yield [0];
        yield [1];
        yield [2];
        yield [-5000];
    }




    /**
     * @covers \DataType\ExtractRule\GetBoolOrDefault
     * @dataProvider provideTestErrorCasesBadTypes
     */
    public function testErrors(mixed $value)
    {
        $rule = new GetBoolOrDefault(false);
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

    public function provideTestErrorCasesBadString()
    {
        yield from getBoolBadStrings();
    }

    /**
     * @covers \DataType\ExtractRule\GetBoolOrDefault
     * @dataProvider provideTestErrorCasesBadString
     */
    public function testErrorsBadStrings($value)
    {
        $rule = new GetBoolOrDefault(false);
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
     * @covers \DataType\ExtractRule\GetBoolOrDefault
     */
    public function testDescription()
    {
        $defaults = [true, false];
        foreach ($defaults as $default) {
            $rule = new GetBoolOrDefault($default);
            $description = $this->applyRuleToDescription($rule);

            $rule->updateParamDescription($description);
            $this->assertSame('boolean', $description->getType());
            $this->assertFalse($description->getRequired());

            $this->assertSame($default, $description->getDefault());
        }
    }
}
