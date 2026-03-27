<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Exception\InvalidDatetimeFormatExceptionData;
use DataType\Messages;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class InvalidDatetimeFormatExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \DataType\Exception\InvalidDatetimeFormatExceptionData
     */
    public function testWorks()
    {
        $exception = InvalidDatetimeFormatExceptionData::stringRequired(
            4, []
        );

        $this->assertStringMatchesTemplateString(
            Messages::ERROR_DATE_FORMAT_MUST_BE_STRING,
            $exception->getMessage()
        );
        $this->assertStringContainsString('array', $exception->getMessage());

        $test_message = "Invalid date interval";
        $test_code = 123;

        if (PHP_VERSION_ID < 80300) {
            $this->markTestSkipped("DateInvalidOperationException only exists on PHP >= 8.3");
        }

        $previous = new \DateInvalidOperationException();
        $exception = InvalidDatetimeFormatExceptionData::invalidTimeOffset(
            $test_message,
            $test_code,
            $previous
        );

        $this->assertStringContainsString($test_message, $exception->getMessage());
    }
}
