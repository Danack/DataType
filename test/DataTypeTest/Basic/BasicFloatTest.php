<?php

namespace DataTypeTest\Basic;

use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

class BasicFloatTest extends BaseTestCase
{
    /**
     * @covers \DataType\Basic\BasicFloat
     */
    public function testWorks()
    {
        $value = 1.234;
        $data = ['float_input' => $value];

        $floatParamTest = BasicFloatFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($value, $floatParamTest->value);
    }
}
