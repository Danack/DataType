<?php

declare(strict_types=1);

namespace DataTypeTest;

use DataType\DataStorage\DataStorage;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Exception\UnknownParamException;
use DataType\Exception\ValidationException;
use DataType\ExtractRule\GetInt;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\InputType;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\AlwaysEndsRule;
use DataType\ProcessRule\AlwaysErrorsRule;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationResult;
use VarMap\ArrayVarMap;
use function DataType\create;
use function DataType\createOrError;
use function DataType\createWithResult;
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
        // @phpstan-ignore argument.type (testing error path with arbitrary class name)
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
            private BaseTestCase $test;
            public function __construct(BaseTestCase $test)
            {
                $this->test = $test;
            }

            public function process($value, ProcessedValues $processedValues, DataStorage $inputStorage) : ValidationResult
            {
                $this->test->fail("This shouldn't be reached.");
                //this code won't be executed.
                /** @phpstan-ignore deadCode.unreachable */
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
            // @phpstan-ignore argument.type (testing error path with arbitrary class name)
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
        $this->assertInstanceOf(\DataTypeTest\Integration\FooParams::class, $fooParams);
        $this->assertEquals(5, $fooParams->getLimit());
    }

    /**
     * @covers ::DataType\createWithResult
     * @covers \DataType\CreateResult
     */
    public function testCreateWithResult_Works(): void
    {
        $dataStorage = TestArrayDataStorage::fromArray(['limit' => 5]);
        $rules = \DataTypeTest\Integration\FooParams::getInputTypes();

        $result = createWithResult(
            \DataTypeTest\Integration\FooParams::class,
            $rules,
            $dataStorage
        );

        $this->assertTrue($result->isValid());
        $this->assertSame([], $result->getErrors());
        $fooParams = $result->getValue();
        $this->assertInstanceOf(\DataTypeTest\Integration\FooParams::class, $fooParams);
        $this->assertSame(5, $fooParams->getLimit());
    }

    /**
     * @covers ::DataType\createWithResult
     * @covers \DataType\CreateResult
     */
    public function testCreateWithResult_ErrorIsReturned(): void
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $rules = \DataTypeTest\Integration\FooParams::getInputTypes();

        $result = createWithResult(
            \DataTypeTest\Integration\FooParams::class,
            $rules,
            $dataStorage
        );

        $this->assertFalse($result->isValid());
        $this->assertNull($result->getValue());
        $errors = $result->getErrors();
        $this->assertCount(1, $errors);
        $this->assertValidationProblem(
            '/limit',
            'Value not set.',
            $errors
        );
    }
}
