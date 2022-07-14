<?php

declare(strict_types = 1);

namespace TypeSpecTest;

use TypeSpec\ValidationProblem;
use TypeSpec\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class ValidationProblemTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ValidationProblem
     */
    public function testWorks()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $key = 'nonexistent';

        $dataStorage = $dataStorage->moveKey($key);
        $problemMessage = 'There was problem';

        $validationProblem = new ValidationProblem($dataStorage, $problemMessage);

        $this->assertSame($problemMessage, $validationProblem->getProblemMessage());
        $this->assertSame($dataStorage, $validationProblem->getInputStorage());
        $this->assertSame('/' . $key . ' ' . $problemMessage, $validationProblem->toString());
    }
}