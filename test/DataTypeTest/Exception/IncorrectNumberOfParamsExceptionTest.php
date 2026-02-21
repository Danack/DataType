<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Exception\IncorrectNumberOfParametersExceptionData;
use DataType\Messages;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class IncorrectNumberOfParamsExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \DataType\Exception\IncorrectNumberOfParametersExceptionData
     */
    public function testWorks()
    {
        $exception = IncorrectNumberOfParametersExceptionData::wrongNumber(
            self::class,
            3,
            4
        );

        $expected_message = sprintf(
            Messages::INCORRECT_NUMBER_OF_PARAMETERS,
            self::class,
            3,
            4
        );

        $this->assertSame(
            $expected_message,
            $exception->getMessage()
        );
    }
}
