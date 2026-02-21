<?php

declare(strict_types = 1);

namespace DataTypeTest\Basic;

use DataType\Basic\TextString;
use DataType\Exception\ValidationException;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use function DataType\createSingleValue;

/**
 * @coversNothing
 * @group wip
 */
class TextStringTest extends BaseTestCase
{
    /**
     * @covers \DataType\Basic\TextString
     */
    public function testWorks()
    {
        $textString = new TextString('john');
        $input = 'something';
        $result = createSingleValue($textString, $input);
        $this->assertSame($input, $result);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::STRING_EXPECTED);
        createSingleValue($textString, 123);
    }
}
