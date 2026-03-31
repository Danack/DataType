<?php

namespace DataTypeTest\Basic;

use DataType\Basic\BasicPhpEnumTypeOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\BasicPhpEnumTypeOrNullFixture;
use DataTypeTestFixture\Basic\TestEnum;

/**
 * @covers \DataType\Basic\BasicPhpEnumTypeOrNull
 */
class BasicPhpEnumTypeOrNullTest extends BaseTestCase
{
    public function testWorksWithValidEnumValue()
    {
        $value = 'VALUE1';
        $data = ['enum_input' => $value];

        $enumParamTest = BasicPhpEnumTypeOrNullFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($value, $enumParamTest->value);
    }

    public function testFailsWithNull()
    {
        try {
            $data = ['enum_input' => null];

            BasicPhpEnumTypeOrNullFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail("Expected ValidationException was not thrown.");
        }
        catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp(
                '/enum_input',
                Messages::STRING_EXPECTED,
                $ve->getValidationProblems()
            );
        }
    }

    public function testWorksWithMissingValue()
    {
        $data = [];

        $enumParamTest = BasicPhpEnumTypeOrNullFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertNull($enumParamTest->value);
    }

    public function testFailsWithInvalidEnumValue()
    {
        try {
            $data = ['enum_input' => 'INVALID_VALUE'];

            BasicPhpEnumTypeOrNullFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail("Expected ValidationException was not thrown.");
        }
        catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp(
                '/enum_input',
                Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
                $ve->getValidationProblems()
            );
        }
    }

    public function testImplementsHasInputType()
    {
        $propertyType = new BasicPhpEnumTypeOrNull('test_name', TestEnum::class);
        $this->assertInstanceOf(\DataType\HasInputType::class, $propertyType);
    }

    public function testGetInputTypeReturnsCorrectType()
    {
        $propertyType = new BasicPhpEnumTypeOrNull('test_name', TestEnum::class);
        $inputType = $propertyType->getInputType();
        
        $this->assertInstanceOf(\DataType\InputType::class, $inputType);
        $this->assertSame('test_name', $inputType->getName());
    }
}
