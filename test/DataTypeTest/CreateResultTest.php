<?php

declare(strict_types=1);

namespace DataTypeTest;

use DataType\CreateResult;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\ValidationProblem;

/**
 * @covers \DataType\CreateResult
 */
class CreateResultTest extends BaseTestCase
{
    public function testSuccess_isValidReturnsTrue(): void
    {
        $obj = new \stdClass();
        $result = CreateResult::success($obj);

        $this->assertTrue($result->isValid());
    }

    public function testSuccess_getValueReturnsInstance(): void
    {
        $obj = new \stdClass();
        $result = CreateResult::success($obj);

        $this->assertSame($obj, $result->getValue());
    }

    public function testSuccess_getErrorsReturnsEmptyArray(): void
    {
        $obj = new \stdClass();
        $result = CreateResult::success($obj);

        $this->assertSame([], $result->getErrors());
    }

    public function testFailure_isValidReturnsFalse(): void
    {
        $dataStorage = TestArrayDataStorage::fromArray([])->moveKey('foo');
        $problems = [new ValidationProblem($dataStorage, 'Error one')];
        $result = CreateResult::failure($problems);

        $this->assertFalse($result->isValid());
    }

    public function testFailure_getValueReturnsNull(): void
    {
        $dataStorage = TestArrayDataStorage::fromArray([])->moveKey('foo');
        $problems = [new ValidationProblem($dataStorage, 'Error one')];
        $result = CreateResult::failure($problems);

        $this->assertNull($result->getValue());
    }

    public function testFailure_getErrorsReturnsProvidedProblems(): void
    {
        $dataStorage = TestArrayDataStorage::fromArray([])->moveKey('foo');
        $problems = [
            new ValidationProblem($dataStorage, 'Error one'),
            new ValidationProblem($dataStorage->moveKey('bar'), 'Error two'),
        ];
        $result = CreateResult::failure($problems);

        $this->assertSame($problems, $result->getErrors());
        $this->assertCount(2, $result->getErrors());
    }
}
