<?php

declare(strict_types=1);

namespace DataTypeTest;

use DataType\DataStorage\DataStorage;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Exception\ValidationException;
use DataType\ExtractRule\GetInt;
use DataType\ExtractRule\GetIntOrDefault;
use DataTypeTest\BaseTestCase;
use DataType\Messages;
use VarMap\ArrayVarMap;
use DataType\ProcessRule\AlwaysEndsRule;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\AlwaysErrorsRule;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationResult;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\InputType;
use DataType\Exception\UnknownParamException;
use function DataType\create;
use function DataType\createOrError;
use function DataType\processInputTypesFromStorage;

/**
 * This is a general test suite for integration type stuff.
 *
 * aka, if you're not sure where a test should go, put it here.
 *
 * @coversNothing
 */
class ParamsTest extends BaseTestCase
{
    /**
     *  todo - covers what?
     */
    public function testWorksBasic()
    {
        $rules = [
            new InputType(
                'foo',
                new GetIntOrDefault(5)
            )
        ];

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);

        $problems = processInputTypesFromStorage(
            $rules,
            $processedValues,
            $dataStorage
        );

        $this->assertNoValidationProblems($problems);
//        $processedValues = \Params\ParamsExecutor::executeRules($rules, new ArrayVarMap([]));
        $this->assertSame(['foo' => 5], $processedValues->getAllValues());
    }

    /**
     *  todo - covers what?
     */
    public function testInvalidInputThrows()
    {
        $arrayVarMap = new ArrayVarMap([]);
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);

        $rules = [
            new InputType(
                'foo',
                new GetInt()
            )
        ];

        $this->expectException(\DataType\Exception\ValidationException::class);
        // TODO - we should output the keys as well.
        $this->expectExceptionMessage("Value not set.");
        create('Foo', $rules, $dataStorage);
    }

    /**
     *  todo - covers what?
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;
        $data = ['foo' => 5];

        $dataStorage = TestArrayDataStorage::fromArray($data);

        $rules = [
            new InputType(
                'foo',
                new GetInt(),
                // This rule will stop processing
                new AlwaysEndsRule($finalValue),
                // this rule would give an error if processing was not stopped.
                new MaxIntValue($finalValue - 5)
            )
        ];

        $processedValues = new ProcessedValues();

        $validationProblems = processInputTypesFromStorage($rules, $processedValues, $dataStorage);
        $this->assertNoValidationProblems($validationProblems);

        $this->assertHasValue($finalValue, 'foo', $processedValues);
    }

    public function testErrorResultStopsProcessing()
    {
        $shouldntBeInvoked = new class($this) implements ProcessRule {
            private $test;
            public function __construct(BaseTestCase $test)
            {
                $this->test = $test;
            }

            public function process($value, ProcessedValues $processedValues, DataStorage $inputStorage) : ValidationResult
            {
                $this->test->fail("This shouldn't be reached.");
                $key = "foo";
                //this code won't be executed.
                return ValidationResult::errorResult($inputStorage, "Shouldn't be called");
            }

            public function updateParamDescription(ParamDescription $paramDescription): void
            {
                // does nothing
            }
        };

        $errorMessage = 'deliberately stopped';
        $data = ['foo' => 100];
        $dataStorage = TestArrayDataStorage::fromArray($data);

        $inputParameters = [
            new InputType(
                'foo',
                new GetInt(),
                // This rule will stop processing
                new AlwaysErrorsRule($errorMessage),
                // this rule would give an error if processing was not stopped.
                $shouldntBeInvoked
            )
        ];

        try {
            create('Foo', $inputParameters, $dataStorage);

            $this->fail("This shouldn't be reached, as an exception should have been thrown.");
        }
        catch (ValidationException $validationException) {
            $this->assertValidationProblem(
                '/foo',
                $errorMessage,
                $validationException->getValidationProblems()
            );
        }
    }


    /**
     * @covers ::DataType\create
     */
    public function testException()
    {
        $rules = \DataTypeTest\Integration\FooParams::getInputTypes();
        $this->expectException(\DataType\Exception\DataTypeException::class);

        $dataStorage =  TestArrayDataStorage::fromArraySetFirstValue([]);


        create(\DataTypeTest\Integration\FooParams::class, $rules, $dataStorage);
    }

    /**
     * @covers ::DataType\create
     */
    public function testWorks()
    {
        $data = ['limit' => 5];
        $dataStorage =  TestArrayDataStorage::fromArray($data);

        $rules = \DataTypeTest\Integration\FooParams::getInputTypes();
        $fooParams = create(
            \DataTypeTest\Integration\FooParams::class,
            $rules,
            $dataStorage
        );

        /** @var \DataTypeTest\Integration\FooParams $fooParams */
        $this->assertEquals(5, $fooParams->getLimit());
    }

    /**
     * @covers ::DataType\createOrError
     */
    public function testCreateOrError_ErrorIsReturned()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $rules = \DataTypeTest\Integration\FooParams::getInputTypes();
        [$params, $validationProblems] = createOrError(
            \DataTypeTest\Integration\FooParams::class,
            $rules,
            $dataStorage
        );
        $this->assertNull($params);

        $this->assertCount(1, $validationProblems);
        /** @var \DataType\ValidationProblem $firstProblem */

        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblem(
            '/limit',
            'Value not set.',
            $validationProblems
        );
    }

    /**
     * @covers ::DataType\createOrError
     */
    public function testcreateOrError_Works()
    {
        $dataStorage = TestArrayDataStorage::fromArray(['limit' => 5]);

        $rules = \DataTypeTest\Integration\FooParams::getInputTypes();
        [$fooParams, $errors] = createOrError(
            \DataTypeTest\Integration\FooParams::class,
            $rules,
            $dataStorage
        );

        $this->assertNoValidationProblems($errors);
        /** @var $fooParams \DataTypeTest\Integration\FooParams */
        $this->assertEquals(5, $fooParams->getLimit());
    }
}
