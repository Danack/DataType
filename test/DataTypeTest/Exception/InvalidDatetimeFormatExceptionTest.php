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
    }
}
