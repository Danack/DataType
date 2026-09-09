<?php

namespace DataTypeTest\Basic;

use DataType\Basic\BasicString;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\BasicShaFixture;

/**
 * @covers \DataTypeTestFixture\Basic\BasicShaFixture
 */
class BasicShaTest extends BaseTestCase
{
    public function testWorks(): void
    {
        $value = '7c4a8d2e9f1b6a3c5d8e0f2a4b6c8d1e3f5a7b9c';
        $data = ['sha_input' => $value];

        $shaParamTest = BasicShaFixture::createFromVarMap(new ArrayVarMap($data));
        $this->assertSame($value, $shaParamTest->value);
    }

    public static function provides_errors()
    {
        yield ['12345', Messages::STRING_EXACT_LENGTH];
        yield ['xyza8d2e9f1b6a3c5d8e0f2a4b6c8d1e3f5a7b9c', Messages::ERROR_PATTERN_MISMATCH];
    }


    #[DataProvider('provides_errors')]
    public function test_errors(string $value, string $expected_error_message): void
    {
        $data = ['sha_input' => $value];

        try {
            $shaParamTest = BasicShaFixture::createFromVarMap(new ArrayVarMap($data));
            $this->assertSame($value, $shaParamTest->value);
        }
        catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp(
                '/sha_input',
                $expected_error_message,
                $ve->getValidationProblems()
            );
        }
    }


//    public function testFailsWithMissingRequiredParameter()
//    {
//        try {
//            $data = [];
//
//            BasicStringFixture::createFromVarMap(new ArrayVarMap($data));
//            $this->fail("Expected ValidationException was not thrown.");
//        }
//        catch (\DataType\Exception\ValidationException $ve) {
//            $this->assertValidationProblems(
//                [['/string_input', Messages::VALUE_NOT_SET]],
//                $ve->getValidationProblems()
//            );
//        }
//    }

//    public function testFailsWithInvalidDataType()
//    {
//        try {
//            $data = ['string_input' => 123];
//
//            BasicStringFixture::createFromVarMap(new ArrayVarMap($data));
//            $this->fail("Expected ValidationException was not thrown.");
//        }
//        catch (\DataType\Exception\ValidationException $ve) {
//            $this->assertValidationProblemRegexp(
//                '/string_input',
//                Messages::STRING_EXPECTED,
//                $ve->getValidationProblems()
//            );
//        }
//    }
//
//    public function testFailsWithNullValue()
//    {
//        try {
//            $data = ['string_input' => null];
//
//            BasicStringFixture::createFromVarMap(new ArrayVarMap($data));
//            $this->fail("Expected ValidationException was not thrown.");
//        }
//        catch (\DataType\Exception\ValidationException $ve) {
//            $this->assertValidationProblemRegexp(
//                '/string_input',
//                Messages::STRING_EXPECTED,
//                $ve->getValidationProblems()
//            );
//        }
//    }
//
//    public function testImplementsHasInputType()
//    {
//        $propertyType = new BasicString('test_name');
//        $this->assertInstanceOf(\DataType\HasInputType::class, $propertyType);
//    }
//
//    public function testGetInputTypeReturnsCorrectType()
//    {
//        $propertyType = new BasicString('test_name');
//        $inputType = $propertyType->getInputType();
//
//        $this->assertInstanceOf(\DataType\InputType::class, $inputType);
//        $this->assertSame('test_name', $inputType->getName());
//    }
}
