<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\DataStorage\TestArrayDataStorage;
use DataTypeTest\BaseTestCase;
use DataType\Exception\ValidationException;
use DataType\ValidationProblem;

/**
 * @coversNothing
 */
class ValidationExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\ValidationException
     */
    public function testGetting()
    {
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);

        $message1 = 'foo was invalid';
        $message2 = 'bar was invalid';

        $validationMessages = [
            new ValidationProblem($dataStorage, $message1),
            new ValidationProblem($dataStorage, $message2)
        ];
        $initialString = 'unit test';

        $exception = new ValidationException(
            $initialString,
            $validationMessages
        );

        $this->assertEquals(
            $validationMessages,
            $exception->getValidationProblems()
        );

        $this->assertSame(0, $exception->getCode());

        $strings = $exception->getValidationProblemsAsStrings();
        $actualStrings = [
            '/ ' . $message1,
            '/ ' . $message2
        ];
        $this->assertSame($actualStrings, $strings);
        $this->assertSame("$initialString / $message1, / $message2", $exception->getMessage());
    }

    public function testMessageIsCorrect()
    {
        $detail_of_problem = "";
        $general_description = 'General description';

        $dataStorage = TestArrayDataStorage::fromArray(['foo' => 'bar']);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $validationProblem = new ValidationProblem($dataStorageAtFoo, $detail_of_problem);

        $exception = new ValidationException(
            $general_description,
            [$validationProblem]
        );

        $message = $exception->getMessage();

        $this->assertStringStartsWith($general_description . " ", $message);
        $this->assertStringContainsString($detail_of_problem, $message);
    }
}
