<?php

namespace DataTypeTest\Basic;

use DataType\Basic\BasicString;
use DataTypeTest\BaseTestCase;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Basic\BasicString
 */
class BasicStringTest extends BaseTestCase
{
    public function testWorks()
    {
        $value = 'test string';
        $data = ['string_input' => $value];

        $stringParamTest = BasicStringFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($value, $stringParamTest->value);
    }

    public function testFailsWithMissingRequiredParameter()
    {
        try {
            $data = [];

            BasicStringFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail("Expected ValidationException was not thrown.");
        }
        catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblems(
                [['/string_input', Messages::VALUE_NOT_SET]],
                $ve->getValidationProblems()
            );
        }
    }

    public function testFailsWithInvalidDataType()
    {
        try {
            $data = ['string_input' => 123];

            BasicStringFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail("Expected ValidationException was not thrown.");
        }
        catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblems(
                [['/string_input', Messages::STRING_EXPECTED]],
                $ve->getValidationProblems()
            );
        }
    }

    public function testFailsWithNullValue()
    {
        try {
            $data = ['string_input' => null];

            BasicStringFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail("Expected ValidationException was not thrown.");
        }
        catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblems(
                [['/string_input', Messages::STRING_REQUIRED_FOUND_NULL]],
                $ve->getValidationProblems()
            );
        }
    }

    public function testImplementsHasInputType()
    {
        $propertyType = new BasicString('test_name');
        $this->assertInstanceOf(\DataType\HasInputType::class, $propertyType);
    }

    public function testGetInputTypeReturnsCorrectType()
    {
        $propertyType = new BasicString('test_name');
        $inputType = $propertyType->getInputType();
        
        $this->assertInstanceOf(\DataType\InputType::class, $inputType);
        $this->assertSame('test_name', $inputType->getName());
    }
}

class BasicStringFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('string_input')]
        public readonly string $value,
    ) {
    }
}
