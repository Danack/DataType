<?php

namespace DataTypeTest\Basic;

use DataType\Basic\BasicString;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\Exception\Runtime\ValidationException;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\BasicStringFixture;
use function DataType\createSingleValue;

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
        catch (\DataType\Exception\Runtime\ValidationException $ve) {
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
        catch (\DataType\Exception\Runtime\ValidationException $ve) {
            $this->assertValidationProblemRegexp(
                '/string_input',
                Messages::STRING_EXPECTED,
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
        catch (\DataType\Exception\Runtime\ValidationException $ve) {
            $this->assertValidationProblemRegexp(
                '/string_input',
                Messages::STRING_EXPECTED,
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

    public function testFailsWhenLongerThanMaxLength()
    {
        $propertyType = new BasicString('string_input', 5);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::STRING_TOO_LONG);
        createSingleValue($propertyType, 'abcdef');
    }

    public function testWorksAtMaxLength()
    {
        $propertyType = new BasicString('string_input', 5);
        $result = createSingleValue($propertyType, 'abcde');
        $this->assertSame('abcde', $result);
    }
}
