<?php

namespace DataTypeTest\Basic;


use DataTypeTest\BaseTestCase;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use VarMap\ArrayVarMap;
use DataType\Basic\OptionalBasicString;

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
        catch (\DataType\Exception\ValidationException $ve) {
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
        catch (\DataType\Exception\ValidationException $ve) {
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
}

class OptionalBasicStringFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalBasicString('string_input')]
        public readonly string|null $value,
    ) {
    }
}