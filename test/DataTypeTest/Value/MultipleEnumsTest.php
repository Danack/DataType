<?php

declare(strict_types=1);

namespace DataTypeTest\Exception\Validator;

use DataTypeTest\BaseTestCase;
use DataType\Value\MultipleEnums;

/**
 * @coversNothing
 */
class MultipleEnumsTest extends BaseTestCase
{
    /**
     * @covers \DataType\Value\MultipleEnums
     */
    public function testBasic()
    {
        $values = [
            'foo',
            'bar'
        ];

        $multipleEnums = new MultipleEnums($values);

        $this->assertEquals($values, $multipleEnums->getValues());
    }
}
