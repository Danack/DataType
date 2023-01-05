<?php

namespace DataTypeTest;

use DataType\Exception\LogicExceptionData;
use DataType\Exception\MissingConstructorParameterNameExceptionData;
use DataType\ExtractRule\GetInt;
use DataType\Messages;
use DataType\ProcessedValue;
use DataTypeTest\Fixtures\ClassThatHasSingleConstructorParameter;
use DataType\Exception\NoConstructorExceptionData;
use DataType\ProcessedValues;
use function Danack\PHPUnitHelper\templateStringToRegExp;
use function DataType\get_all_constructor_parameters;

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
}
