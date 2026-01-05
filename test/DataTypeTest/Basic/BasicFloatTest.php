<?php

namespace DataTypeTest\Basic;

use DataType\Basic\BasicFloat;
use DataTypeTest\BaseTestCase;
use DataType\Create\CreateFromVarMap;
use DataType\GetInputTypesFromAttributes;
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
