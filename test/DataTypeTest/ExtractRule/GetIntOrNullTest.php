<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetIntOrNull;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetIntOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetIntOrNull
     */
    public function testMissingGivesError()
    {
        $rule = new GetIntOrNull();
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
            [5, 5],
            [null, null]
        ];
    }

    /**
     * @covers \DataType\ExtractRule\GetIntOrNull
     * @param int|string|null $input
     * @param int|null $expectedValue
     */
    #[DataProvider('provideTestWorksCases')]
    public function testWorks($input, $expectedValue)
    {
        $validator = new ProcessedValues();
        $rule = new GetIntOrNull();
        $dataStorage  = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input);

        $validationResult = $rule->process(
            $validator, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }


    public static function provideTestErrorCases()
    {
        yield ['', Messages::INT_REQUIRED_FOUND_EMPTY_STRING];
        yield ['6 apples', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
        yield ['banana', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
    }

    /**
     * @covers \DataType\ExtractRule\GetIntOrNull
     * @param string $input
     * @param string $message
     */
    #[DataProvider('provideTestErrorCases')]
    public function testErrors($input, $message)
    {
        $rule = new GetIntOrNull();
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
     * @covers \DataType\ExtractRule\GetIntOrNull
     */
    public function testDescription()
    {
        $rule = new GetIntOrNull();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('integer', $description->getType());
        $this->assertTrue($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
