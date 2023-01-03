<?php

declare(strict_types = 1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ExtractRule\GetArrayOfFloat;
use TypeSpec\ProcessedValues;
use TypeSpec\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetArrayOfFloatTest extends BaseTestCase
{
    /**
     * @covers  \TypeSpec\ExtractRule\GetArrayOfFloat
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
     * @covers \TypeSpec\ExtractRule\GetArrayOfInt
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
     * @covers \TypeSpec\ExtractRule\GetArrayOfFloat
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
     * @covers  \TypeSpec\ExtractRule\GetArrayOfFloat
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
     * @covers \TypeSpec\ExtractRule\GetArrayOfFloat
     */
    public function testDescription()
    {
        $rule = new GetArrayOfFloat();
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }
}
