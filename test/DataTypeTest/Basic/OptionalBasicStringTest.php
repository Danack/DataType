<?php

namespace DataTypeTest\Basic;

use DataType\Basic\OptionalBasicString;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\Exception\Runtime\ValidationException;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\OptionalBasicStringFixture;
use function DataType\createSingleValue;

/**
 * @covers \DataType\Basic\OptionalBasicString
 */
class OptionalBasicStringTest extends BaseTestCase
{
    public function testWorks()
    {
        $value = 'test string';
        $data = ['string_input' => $value];

        $stringParamTest = OptionalBasicStringFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($value, $stringParamTest->value);
    }

    public function testWorksWithMissingRequiredParameter()
    {
        $data = [];

        $stringParamTest = OptionalBasicStringFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertNull($stringParamTest->value);
    }

    public function testFailsWithInvalidDataType()
    {
        try {
            $data = ['string_input' => 123];

            OptionalBasicStringFixture::createFromVarMap(new ArrayVarMap($data));
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

    /**
     * The string is optional - if it is set, it should be valid. But it's allowed to be missing.
     */
    public function testFailsWithNullValue()
    {
        try {
            $data = ['string_input' => null];

            OptionalBasicStringFixture::createFromVarMap(new ArrayVarMap($data));
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
        $propertyType = new OptionalBasicString('test_name');
        $this->assertInstanceOf(\DataType\HasInputType::class, $propertyType);
    }

    public function testGetInputTypeReturnsCorrectType()
    {
        $propertyType = new OptionalBasicString('test_name');
        $inputType = $propertyType->getInputType();
        
        $this->assertInstanceOf(\DataType\InputType::class, $inputType);
        $this->assertSame('test_name', $inputType->getName());
    }

    public function testFailsWhenLongerThanMaxLength()
    {
        $propertyType = new OptionalBasicString('string_input', 5);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::STRING_TOO_LONG);
        createSingleValue($propertyType, 'abcdef');
    }

    public function testWorksAtMaxLength()
    {
        $propertyType = new OptionalBasicString('string_input', 5);
        $result = createSingleValue($propertyType, 'abcde');
        $this->assertSame('abcde', $result);
    }
}
