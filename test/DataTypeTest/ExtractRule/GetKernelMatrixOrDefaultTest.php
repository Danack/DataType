<?php

declare(strict_types = 1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Exception\JsonDecodeException;
use DataType\ExtractRule\GetKernelMatrixOrDefault;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetKernelMatrixOrDefaultTest extends BaseTestCase
{
    const UNIT_MATRIX = [[1]];

    public function provideTestWorks()
    {
        $expected = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];
        yield [json_encode($expected), $expected, self::UNIT_MATRIX];
    }

    /**
     * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     * @param string $input
     * @param array<int, array<int, float|int>> $expected
     * @param array<int, array<int, float|int>> $default
     */
    public function testWorks($input, $expected, $default)
    {
        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input);

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expected);
    }


    /**
     * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     */
    public function testMissingGivesDefault()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];


        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::createMissing('foo');

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $default);
    }

    /**
     * * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testInvalidMatrixRows()
    {
        $default = [
            [1, 2, 3],
            'John'
        ];

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::MATRIX_INVALID_BAD_ROW
        );

        new GetKernelMatrixOrDefault($default);
    }

    /**
     * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testInvalidMatrixCell()
    {
        $default = [
            [1, 2, 3],
            [1, 2, 'John'],
        ];

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::MATRIX_INVALID_BAD_CELL
        );

        new GetKernelMatrixOrDefault($default);
    }



    /**
     * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testDescription()
    {
        $default = [[1.5]];

        $rule = new GetKernelMatrixOrDefault($default);
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }



    /**
     * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     */
    public function testBadInput_not_a_string()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];

        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 123);

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::BAD_TYPE_FOR_KERNEL_MATRIX_PROCESS_RULE
        );

        $rule->process(
            $validator,
            $dataStorage
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testErrors_invalidJson()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];

        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition(
            'foo',
            "this is not valid json {}{"
        );

        $this->expectException(JsonDecodeException::class);

        $rule->process(
            $validator,
            $dataStorage
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testErrors_not_2d_array()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];

        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition(
            'foo',
            json_encode("I am not an array json data")
        );

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::KERNEL_MATRIX_ARRAY_EXPECTED,
            $validationResult->getValidationProblems()
        );

        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testErrors_row_is_not_array()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];

        $data_second_row_invalid = [
            [1, 1, 1],
            0,
            [1, 1, 1]
        ];

        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition(
            'foo',
            json_encode($data_second_row_invalid)
        );

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::KERNEL_MATRIX_ERROR_AT_ROW_2D_EXPECTED,
            $validationResult->getValidationProblems()
        );
        $validationProblem = $validationResult->getValidationProblems()[0];
        $this->assertStringContainsString("row 1", $validationProblem->getProblemMessage());

        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testErrors_value_is_not_number()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];

        $data_second_row_invalid = [
            [1, 1, 1],
            [1, 1, 1],
            [1, 2, 'john'],
        ];

        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition(
            'foo',
            json_encode($data_second_row_invalid)
        );

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::KERNEL_MATRIX_ERROR_AT_ROW_COLUMN_NUMBER_EXPECTED,
            $validationResult->getValidationProblems()
        );
        $validationProblem = $validationResult->getValidationProblems()[0];
        $this->assertStringContainsString(
            "Row 2 column 2",
            $validationProblem->getProblemMessage()
        );

        $this->assertNull($validationResult->getValue());
    }
}
