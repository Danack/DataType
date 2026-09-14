<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Exception\Logic\InvalidLocationExceptionData;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class InvalidLocationExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\Logic\InvalidLocationExceptionData
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
     * @covers \DataType\Exception\Logic\InvalidLocationExceptionData
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
