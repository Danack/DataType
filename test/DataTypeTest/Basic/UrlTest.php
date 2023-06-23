<?php

declare(strict_types = 1);

namespace DataTypeTest\Basic;

use DataTypeTest\BaseTestCase;
use DataType\Basic\Url;
use function DataType\createSingleValue;
use DataType\Exception\ValidationException;
use DataType\Messages;

/**
 * @coversNothing
 * @group wip
 */
class UrlTest extends BaseTestCase
{
    /**
     * @covers \DataType\Basic\Url
     */
    public function testWorks()
    {
        $textString = new Url('john');
        $input = 'http://www.google.com';
        $result = createSingleValue($textString, $input);
        $this->assertSame($input, $result);
    }

    public function providesErrors()
    {
        yield ['John', Messages::ERROR_INVALID_URL];
        yield [null, Messages::STRING_REQUIRED_FOUND_NULL];
        yield [123, Messages::STRING_EXPECTED];
    }

    /**
     * @covers \DataType\Basic\Url
     * @dataProvider providesErrors
     */
    public function testErrors($invalid_input, $expected_problem)
    {
        $textString = new Url('john');
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatchesTemplateString($expected_problem);
        createSingleValue($textString, $invalid_input);
    }
}
