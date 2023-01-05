<?php

declare(strict_types = 1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetArrayOfFloat;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetArrayOfFloatTest extends BaseTestCase
{
    /**
     * @covers  \DataType\ExtractRule\GetArrayOfFloat
     */
    public function testWorks()
    {
        $data = [5.5, 6.0, 7.0];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfFloat();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());
        $this->assertSame($data, $result->getValue());
    }


    /**
     * @covers \DataType\ExtractRule\GetArrayOfInt
     */
    public function testMissingGivesError()
    {

        $rule = new GetArrayOfFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::ERROR_MESSAGE_NOT_SET]
        );
    }


    /**
     * @covers \DataType\ExtractRule\GetArrayOfFloat
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfFloat();
        $validator = new ProcessedValues();

        $input = 'banana';

        $validationResult = $rule->process(
            $validator,
            $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::ERROR_MESSAGE_NOT_ARRAY]
        );
    }






    /**
     * @covers  \DataType\ExtractRule\GetArrayOfFloat
     */
    public function testErrorsOnType()
    {
        $data = [5.0, 6.0, 7.0, 'banana'];

        $rule = new GetArrayOfFloat();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblem(
            '/3',
            Messages::FLOAT_REQUIRED,
            $validationProblems
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetArrayOfFloat
     */
    public function testDescription()
    {
        $rule = new GetArrayOfFloat();
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }
}
