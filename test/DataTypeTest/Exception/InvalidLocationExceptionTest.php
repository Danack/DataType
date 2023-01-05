<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataTypeTest\BaseTestCase;
use DataType\Exception\InvalidLocationExceptionData;

/**
 * @coversNothing
 */
class InvalidLocationExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\InvalidLocationExceptionData
     */
    public function testWorksBadArray()
    {
        $location = ['foo', 'bar'];

        $exception = InvalidLocationExceptionData::badArrayDataStorage(
            $location
        );

        $this->assertSame(
            $location,
            $exception->getLocation()
        );
        $this->assertStringContainsString(implode(", ", $location), $exception->getMessage());
    }

    /**
     * @covers \DataType\Exception\InvalidLocationExceptionData
     */
    public function testWorksBadComplex()
    {
        $location = ['foo', 'bar'];

        $exception = InvalidLocationExceptionData::badComplexDataStorage(
            $location
        );

        $this->assertSame(
            $location,
            $exception->getLocation()
        );
        $this->assertStringContainsString(implode(", ", $location), $exception->getMessage());
    }
}
