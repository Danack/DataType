<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Exception\DataTypeLogicException;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\MaximumCount;
use DataTypeTest\BaseTestCase;
use function Danack\PHPUnitHelper\templateStringToRegExp;

/**
 * @coversNothing
 */
class MaximumCountTest extends BaseTestCase
{
    public static function provideWorksCases()
    {
        return [
            [3, []], // 3 <= 3
            [3, [1, 2, 3]], // 3 <= 3
            [4, [1, 2, 3]], // 3 <= 4
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MaximumCount
     * @param array<int, mixed> $values
     */
    #[DataProvider('provideWorksCases')]
    public function testWorks(int $maximumCount, array $values)
    {
        $rule = new MaximumCount($maximumCount);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $values, $processedValues, TestArrayDataStorage::fromArray([$values])
        );
        $this->assertNoProblems($validationResult);
        $this->assertFalse($validationResult->isFinalResult());
        $this->assertSame($values, $validationResult->getValue());
    }

    public static function provideFailsCases()
    {
        return [
            [0, [1, 2, 3]], // 3 > 0
            [3, [1, 2, 3, 4]], // 4 > 3
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MaximumCount
     * @param array<int, mixed> $values
     */
    #[DataProvider('provideFailsCases')]
    public function testFails(int $maximumCount, $values)
    {
        $rule = new MaximumCount($maximumCount);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $values, $processedValues, TestArrayDataStorage::fromArray([$values])
        );
        $this->assertNull($validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());

//        'Number of elements in foo is too large. Max allowed is 0 but got 3.'

//        $this->assertRegExp(
//            stringToRegexp(MaximumCount::ERROR_TOO_MANY_ELEMENTS),
//            $validationResult->getValidationProblems()['/foo']
//        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertValidationProblemRegexp(
            '/',
            Messages::ERROR_TOO_MANY_ELEMENTS,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\MaximumCount
     */
    public function testMinimimCountZero()
    {
        $this->expectException(DataTypeLogicException::class);
        $this->expectExceptionMessage(Messages::ERROR_MAXIMUM_COUNT_MINIMUM);
        new MaximumCount(-2);
    }

    /**
     * @covers \DataType\ProcessRule\MaximumCount
     */
    public function testInvalidOperand()
    {
        $rule = new MaximumCount(3);
        $this->expectException(DataTypeLogicException::class);

        $processedValues = new ProcessedValues();
        $this->expectExceptionMessageMatches(
            templateStringToRegExp(Messages::ERROR_WRONG_TYPE_VARIANT_1)
        );

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);

        $rule->process(
            'a banana', $processedValues, $dataStorage
        );
    }

    /**
     * @covers \DataType\ProcessRule\MaximumCount
     */
    public function testDescription()
    {
        $rule = new MaximumCount(3);
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame(3, $description->getMinItems());
    }
}
