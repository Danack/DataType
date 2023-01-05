<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\Order;
use DataType\Value\Ordering;
use DataType\ProcessedValues;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;

/**
 * @coversNothing
 */
class OrderTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['time', ['time' => Ordering::ASC], false],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \DataType\ProcessRule\Order
     */
    public function testValidation($testValue, $expectedOrdering, $expectError)
    {
        $orderParams = ['time', 'distance'];

        $rule = new Order($orderParams);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $value = $validationResult->getValue();
        $this->assertInstanceOf(Ordering::class, $value);
        /** @var $value Ordering */
        $this->assertEquals($expectedOrdering, $value->toOrderArray());
    }

    public function provideTestErrors()
    {
        return [
            ['bar', null, true],
        ];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\Order
     */
    public function testErrors($testValue, $expectedOrdering, $expectError)
    {
        $orderParams = ['time', 'distance'];

        $rule = new Order($orderParams);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ORDER_VALUE_UNKNOWN,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString(
            $validationResult,
            implode(", ", $orderParams)
        );
    }

    /**
     * @covers \DataType\ProcessRule\Order
     */
    public function testDescription()
    {
        $orderParams = ['time', 'distance'];
        $rule = new Order($orderParams);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(
            ParamDescription::COLLECTION_CSV,
            $description->getCollectionFormat()
        );
    }
}
