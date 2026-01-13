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
        $textString = new Url('a_data_type');
        $input = 'http://www.google.com';
        $result = createSingleValue($textString, $input);
        $this->assertSame($input, $result);

        $textString = new Url('a_data_type');
        $input = 'http://t.ly/';
        $result = createSingleValue($textString, $input);
        $this->assertSame($input, $result);


        $textString = new Url('a_data_type');
        $input = 'http://www.google.com/';
        $input = str_pad($input, 2048, 'a');
        $result = createSingleValue($textString, $input);
        $this->assertSame($input, $result);
    }

    public function providesErrors()
    {
        $input = str_pad('http://www.google.com/', 2049, 'a');

        yield ['Too_short__', Messages::STRING_TOO_SHORT];
        yield [$input, Messages::STRING_TOO_LONG];
        yield ['Some_string_that_is_longer_than_12_chars', Messages::ERROR_INVALID_URL];
        yield [null, Messages::STRING_EXPECTED];
        yield [123, Messages::STRING_EXPECTED];

        // Missing scheme
        yield ['www.google.com', Messages::ERROR_INVALID_URL];
    }

    /**
     * @covers \DataType\Basic\Url
     * @dataProvider providesErrors
     * @param mixed $invalid_input
     * @param string $expected_problem
     */
    public function testErrors($invalid_input, $expected_problem)
    {
        $textString = new Url('john');
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatchesTemplateString($expected_problem);
        createSingleValue($textString, $invalid_input);
    }
}
