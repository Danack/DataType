<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function provideTestWorksCases()
    {
        yield from getBoolTestWorks();
        yield [null, null];
    }

    /**
     * @covers \DataType\ExtractRule\GetBoolOrNull
     * @param bool|string|null $input
     * @param bool|null $expectedValue
     */
    #[DataProvider('provideTestWorksCases')]
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

    public static function provideTestErrorCases()
    {
        return [
            [fopen('php://memory', 'r+')],
            [[1, 2, 3]],
            [new \stdClass()]
        ];
    }

    /**
     * @covers \DataType\ExtractRule\GetBoolOrNull
     * @param mixed $value
     */
    #[DataProvider('provideTestErrorCases')]
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

    public static function provideTestErrorCasesForBadStrings()
    {
        yield from getBoolBadStrings();
    }

    /**
     * @covers \DataType\ExtractRule\GetBoolOrNull
     * @param string $value
     */
    #[DataProvider('provideTestErrorCasesForBadStrings')]
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
