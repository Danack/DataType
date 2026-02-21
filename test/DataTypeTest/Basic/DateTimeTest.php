<?php

declare(strict_types = 1);

namespace DataTypeTest\Basic;

use DataType\Basic\DateTime as DateTimeProcess;
use DataTypeTest\BaseTestCase;
use function DataType\createSingleValue;

/**
 * @coversNothing
 */
class DateTimeTest extends BaseTestCase
{
    /**
     * @return \Generator<array{0: string, 1: string}>
     */
    public function provideTestWorksCases(): \Generator
    {
        $date1 = \DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T10:00:00-05:00');
        $date2 = \DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T15:00:00Z');

        if ($date1 === false || $date2 === false) {
            throw new \RuntimeException('Failed to create DateTime from format');
        }

        yield [
            '2002-10-02T10:00:00-05:00',
            $date1->format("Y-m-d H:i:s")
        ];

        yield [
            '2002-10-02T15:00:00Z',
            $date2->format("Y-m-d H:i:s")
        ];
    }


    /**
     * @covers \DataType\Basic\DateTime
     * @dataProvider provideTestWorksCases
     * @param string $input
     * @param string $expected_output
     */
    public function testWorks(string $input, string $expected_output)
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
