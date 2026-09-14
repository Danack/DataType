<?php

declare(strict_types = 1);

namespace DataTypeTest\Basic;

use DataType\Basic\TextString;
use DataType\Exception\Runtime\ValidationException;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use function DataType\createSingleValue;

/**
 * @coversNothing
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

    /**
     * @covers \DataType\Basic\TextString
     */
    public function testFailsWhenLongerThanMaxLength()
    {
        $textString = new TextString('john', 5);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::STRING_TOO_LONG);
        createSingleValue($textString, 'abcdef');
    }

    /**
     * @covers \DataType\Basic\TextString
     */
    public function testWorksAtMaxLength()
    {
        $textString = new TextString('john', 5);
        $result = createSingleValue($textString, 'abcde');
        $this->assertSame('abcde', $result);
    }
}
