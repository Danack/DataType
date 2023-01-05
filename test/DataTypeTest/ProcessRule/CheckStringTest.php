<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\Exception\InvalidRulesExceptionData;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\CheckString;
use DataType\Messages;

/**
 * @coversNothing
 */
class CheckStringTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessRule\CheckString
     */
    public function testWorks()
    {
        $obj = new class {
            use CheckString;
        };

        $result = $obj->checkString("foo");
        $this->assertIsString($result);

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(
            Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );
        $obj->checkString(5);
    }

    /**
     * @covers \DataType\ProcessRule\CheckString
     */
    public function testStringableObjectWorks()
    {
        $obj = new class {
            use CheckString;
        };

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(
            Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );

        $obj->checkString(new \StdClass());
    }

    /**
     * @covers \DataType\ProcessRule\CheckString
     */
    public function testStdClassFails()
    {
        $obj = new class {
            use CheckString;
        };

        $inputString = "foo";

        $someString = new class($inputString) implements \stringable {
            public function __construct(private string $name)
            {
            }

            public function __toString()
            {
                return $this->name;
            }
        };


        $result = $obj->checkString($someString);
        $this->assertSame($inputString, $result);
    }
}
