<?php

namespace DataTypeTest;

use DataType\Exception\LogicExceptionData;
use DataType\Exception\MissingConstructorParameterNameExceptionData;
use DataType\ExtractRule\GetInt;
use DataType\Exception\JsonEncodeException;
use DataType\Exception\JsonDecodeException;
use DataType\Messages;
use DataType\ProcessedValue;
use DataTypeTest\Fixtures\ClassThatHasSingleConstructorParameter;
use DataType\ProcessedValues;
use function Danack\PHPUnitHelper\templateStringToRegExp;
use function DataType\get_all_constructor_parameters;
use function DataType\json_decode_safe;
use function DataType\json_encode_safe;

class FunctionsInternalTest extends BaseTestCase
{
    /**
     * @covers ::\DataType\get_all_constructor_parameters
     * @throws LogicExceptionData
     */
    public function test_get_all_constructor_parameters_works()
    {
        $classname = ClassThatHasSingleConstructorParameter::class;

        $dataType = new \DataType\InputType(
            'value',
            new GetInt()
        );
        $processedValue = new ProcessedValue($dataType, 4);

        $processedValues = ProcessedValues::fromArray([$processedValue]);

        $reflection_class = new \ReflectionClass($classname);
        $r_constructor = $reflection_class->getConstructor();
        if ($r_constructor === null) {
            $this->fail("Test should never fail, we are using a known good class.");
        }
        $constructor_parameters = $r_constructor->getParameters();

        $built_parameters = get_all_constructor_parameters(
            $classname,
            $constructor_parameters,
            $processedValues
        );

        $this->assertSame([0 => 4], $built_parameters);
    }

    /**
     * @covers ::\DataType\get_all_constructor_parameters
     * @throws LogicExceptionData
     */
    public function test_get_all_constructor_parameters_errors()
    {
        $classname = ClassThatHasSingleConstructorParameter::class;

        $processedValues = ProcessedValues::fromArray([]);

        $reflection_class = new \ReflectionClass($classname);
        $r_constructor = $reflection_class->getConstructor();
        if ($r_constructor === null) {
            $this->fail("Test should never fail, we are using a known good class.");
        }
        $constructor_parameters = $r_constructor->getParameters();

        $this->expectException(MissingConstructorParameterNameExceptionData::class);
        $this->expectErrorMessageMatches(
            templateStringToRegExp(Messages::MISSING_PARAMETER_NAME)
        );

        get_all_constructor_parameters(
            $classname,
            $constructor_parameters,
            $processedValues
        );
    }


    /**
     * @covers ::\DataType\json_encode_safe
     * @covers ::\DataType\json_decode_safe
     */
    public function test_json_encode_safe()
    {
        $data = [1, 2, 3];

        $json = json_encode_safe($data);

        $outputData = json_decode_safe($json);
        $this->assertSame($data, $outputData);
    }

    /**
     * @covers ::\DataType\json_encode_safe
     */
    public function test_json_encode_safe_errors()
    {
        $data = [1, 2, fopen("php://input", "r")];

        $this->expectException(JsonEncodeException::class);
        json_encode_safe($data);
    }

    /**
     * @covers ::\DataType\json_decode_safe
     */
    public function test_json_decode_safe_errors_with_null()
    {
        $this->expectException(JsonDecodeException::class);
        json_decode_safe(null);
    }

    /**
     * @covers ::\DataType\json_decode_safe
     */
    public function test_json_decode_safe_errors_with_null_data()
    {
        $this->expectException(JsonDecodeException::class);
        $data = json_encode(null);
        if ($data === false) {
            throw new \Exception("json_encode failed");
        }
        json_decode_safe($data);
    }

    /**
     * @covers ::\DataType\json_decode_safe
     */
    public function test_json_decode_safe_errors_with_bad_data()
    {
        $this->expectException(JsonDecodeException::class);
        json_decode_safe("{ foo");
    }
}
