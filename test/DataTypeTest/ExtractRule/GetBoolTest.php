<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetBool;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetBoolTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetBool
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

    public static function provideTestWorksCases()
    {
        yield from getBoolTestWorks();
    }

    /**
     * @covers \DataType\ExtractRule\GetBool
     * @param bool|string $input
     * @param bool $expectedValue
     */
    #[DataProvider('provideTestWorksCases')]
    public function testWorks(bool|string $input, bool $expectedValue)
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

    public static function provideTestErrorCases()
    {
        return [
            // todo - we should test the exact error.
            [fopen('php://memory', 'r+')],
            [[1, 2, 3]],
            [new \stdClass()]
        ];
    }

    /**
     * @covers \DataType\ExtractRule\GetBool
     * @param mixed $value
     */
    #[DataProvider('provideTestErrorCases')]
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

    public static function provideTestErrorCasesForBadStrings()
    {
        yield from getBoolBadStrings();
    }

    /**
     * @covers \DataType\ExtractRule\GetBool
     * @param string $value
     */
    #[DataProvider('provideTestErrorCasesForBadStrings')]
    public function testErrorsWithBadStrings(string $value)
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
     * @covers \DataType\ExtractRule\GetBool
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
