<?php

namespace DataTypeTestFixture\Basic;

use DataType\Basic\BasicPhpEnumTypeOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

enum TestEnum: string
{
    case VALUE1 = 'VALUE1';
    case VALUE2 = 'VALUE2';
    case VALUE3 = 'VALUE3';
}
