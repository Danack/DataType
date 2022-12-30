<?php

declare(strict_types = 1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ExtractRule\GetInt;
use TypeSpec\ProcessedValues;
use TypeSpec\ExtractRule\GetKernelMatrixOrDefault;

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
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
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
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
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
     * * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
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
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
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
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testDescription()
    {
        $default = [[1.5]];

        $rule = new GetKernelMatrixOrDefault($default);
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }



    /**
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
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
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
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

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::INVALID_JSON_FOR_KERNEL_MATRIX_PROCESS_RULE,
            $validationResult->getValidationProblems()
        );


        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
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
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
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
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
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
            [1, 'john', 2],
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
            "Row 2 column 1",
            $validationProblem->getProblemMessage()
        );

        $this->assertNull($validationResult->getValue());
    }

    public function testErrors_5()
    {
        /*
         * oreach ($row as $value) {
94
                $floatRuleResult = $floatRule->process(
95
                    $value,
96
                    $processedValues,
97
                    $dataStorage
98
                );
99
100
                if ($floatRuleResult->anyErrorsFound()) {
101
                    foreach ($floatRuleResult->getValidationProblems() as $validationProblem) {
102
                        $validationProblems[] = $validationProblem;
103
                    }
104
                }
105
            }
                */
    }

    public function testErrors_6()
    {
        /*
         * if (count($validationProblems) !== 0) {
111
            return ValidationResult::fromValidationProblems($validationProblems);
112

                */
    }
}
