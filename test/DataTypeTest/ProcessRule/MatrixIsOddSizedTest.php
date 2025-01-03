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
use DataType\ProcessRule\MatrixIsOddSized;

/**
 * Checks that a matrix has an odd number of both rows and columns.
 * This is a useful check for image processing, where there needs to
 * be a center position, that represents the current pixel.
 * @coversNothing
 */
class MatrixIsOddSizedTest extends BaseTestCase
{
    public function provideTestCases()
    {
        $values3 = [
            [1, 2, 3],
            [1, 2, 3],
            [1, 2, 3],
        ];

        $values1 = [
            [1],
        ];

        yield [$values1];
        yield [$values3];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \DataType\ProcessRule\MatrixIsOddSized
     */
    public function testValidation($testValue)
    {
        $rule = new MatrixIsOddSized();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    public function provideTestErrors()
    {
        $values2x3 = [
            [1, 2, 3],
            [1, 2, 3],
        ];

        $values3x2 = [
            [1, 2],
            [1, 2],
            [2, 3],
        ];

        yield [$values2x3, Messages::MATRIX_MUST_BE_ODD_SIZED_ROWS_ARE_EVEN];
        yield [$values3x2, Messages::MATRIX_MUST_BE_ODD_SIZED_COLUMNS_ARE_EVEN];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \DataType\ProcessRule\MatrixIsOddSized
     */
    public function testErrors($testValue, $expectedErrorMessage)
    {
        $rule = new MatrixIsOddSized();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblem(
            '/foo',
            $expectedErrorMessage,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\MatrixIsOddSized
     */
    public function testDescription()
    {
        $rule = new MatrixIsOddSized();
        $description = $this->applyRuleToDescription($rule);
    }
}
