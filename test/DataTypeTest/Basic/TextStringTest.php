<?php

declare(strict_types = 1);

namespace DataTypeTest\Basic;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\Basic\TextString;
use function DataType\createSingleValue;
use DataType\Exception\ValidationException;

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
