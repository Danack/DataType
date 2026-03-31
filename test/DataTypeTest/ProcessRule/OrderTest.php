<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\Order;
use DataType\Value\Ordering;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class OrderTest extends BaseTestCase
{
    public static function provideTestCases()
    {
        return [
            ['time', ['time' => Ordering::ASC]],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\Order
     * @param array<int, string> $expectedOrdering
     */
    #[DataProvider('provideTestCases')]
    public function testValidation(string $testValue, array $expectedOrdering)
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

    public static function provideTestErrors()
    {
        yield ['bar'];
    }

    /**
     * @covers \DataType\ProcessRule\Order
     */
    #[DataProvider('provideTestErrors')]
    public function testErrors(string $testValue)
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
