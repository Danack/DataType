<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetInt;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class GetIntTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetInt
     */
    public function testMissingGivesError()
    {
        $rule = new GetInt();
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
            [5, 5],
        ];
    }

    /**
     * @covers \DataType\ExtractRule\GetInt
     * @dataProvider provideTestWorksCases
     * @param int|string $input
     * @param int $expectedValue
     */
    public function testWorks($input, $expectedValue)
    {
        $validator = new ProcessedValues();
        $rule = new GetInt();
        $dataStorage  = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input);

        $validationResult = $rule->process(
            $validator, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }


    public function provideTestErrorCases()
    {
        yield [null, Messages::INT_REQUIRED_UNSUPPORTED_TYPE];
        yield ['', Messages::INT_REQUIRED_FOUND_EMPTY_STRING];
        yield ['6 apples', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
        yield ['banana', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
    }

    /**
     * @covers \DataType\ExtractRule\GetInt
     * @dataProvider provideTestErrorCases
     * @param mixed $input
     * @param string $message
     */
    public function testErrors($input, $message)
    {
        $rule = new GetInt();
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
     * @covers \DataType\ExtractRule\GetInt
     */
    public function testDescription()
    {
        $rule = new GetInt();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('integer', $description->getType());
        $this->assertTrue($description->getRequired());
    }
}
