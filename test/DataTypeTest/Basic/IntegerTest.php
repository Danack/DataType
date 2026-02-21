<?php

declare(strict_types = 1);

namespace DataTypeTest\Basic;

use DataType\Basic\Integer;
use DataType\Exception\ValidationException;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use function DataType\createSingleValue;

/**
 * @coversNothing
 */
class IntegerTest extends BaseTestCase
{
    /**
     * @covers \DataType\Basic\Integer
     */
    public function testWorks()
    {
        $integer = new Integer('john');
        $input = 123;
        $result = createSingleValue($integer, $input);
        $this->assertSame($input, $result);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::INT_REQUIRED_FOUND_NON_DIGITS2);
        createSingleValue($integer, "John");
    }
}
