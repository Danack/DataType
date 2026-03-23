<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Exception\ClassInvalidException;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class ClassInvalidExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\ClassInvalidException
     */
    public function testClassNotFound(): void
    {
        $typeString = 'MissingClassName';
        $exception = ClassInvalidException::classNotFound($typeString);

        $this->assertSame(
            sprintf(ClassInvalidException::CLASS_DOESNT_EXIST_MESSAGE, $typeString),
            $exception->getMessage()
        );
    }

    /**
     * @covers \DataType\Exception\ClassInvalidException
     */
    public function testClassIsNotEnum(): void
    {
        $typeString = \stdClass::class;
        $exception = ClassInvalidException::classIsNotEnum($typeString);

        $this->assertSame(
            sprintf(ClassInvalidException::CLASS_IS_NOT_ENUM, $typeString),
            $exception->getMessage()
        );
    }
}
