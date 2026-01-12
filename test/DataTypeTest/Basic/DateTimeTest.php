<?php

declare(strict_types = 1);

namespace DataTypeTest\Basic;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\Basic\DateTime as DateTimeProcess;
use function DataType\createSingleValue;
use DataType\Exception\ValidationException;

/**
 * @coversNothing
 */
class DateTimeTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        yield [
            '2002-10-02T10:00:00-05:00',
            (\DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T10:00:00-05:00'))->format("Y-m-d H:i:s")
        ];

        yield [
            '2002-10-02T15:00:00Z',
            (\DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T15:00:00Z'))->format("Y-m-d H:i:s")
        ];
    }


    /**
     * @covers \DataType\Basic\DateTime
     * @dataProvider provideTestWorksCases
     */
    public function testWorks(string $input, \DateTimeInterface $expected_output)
    {
        $integer = new DateTimeProcess('john');

        $result = createSingleValue($integer, $input);
        /**
         * @var \DateTime $result
         */
        $this->assertSame($result->format("Y-m-d H:i:s"), $expected_output);

//        $this->expectException(ValidationException::class);
//        $this->expectExceptionMessageMatchesTemplateString(Messages::INT_REQUIRED_FOUND_NON_DIGITS2);
//        createSingleValue($integer, "John");
    }
}
