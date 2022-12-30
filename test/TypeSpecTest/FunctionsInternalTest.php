<?php

namespace TypeSpecTest;

use TypeSpec\Exception\LogicException;
use TypeSpec\Exception\MissingConstructorParameterNameException;
use TypeSpec\ExtractRule\GetInt;
use TypeSpec\Messages;
use TypeSpec\ProcessedValue;
use TypeSpecTest\Fixtures\ClassThatHasSingleConstructorParameter;
use TypeSpec\Exception\NoConstructorException;
use TypeSpec\ProcessedValues;
use function Danack\PHPUnitHelper\templateStringToRegExp;
use function TypeSpec\get_all_constructor_parameters;

class FunctionsInternalTest extends BaseTestCase
{
    /**
     * @covers ::\TypeSpec\get_all_constructor_parameters
     * @throws LogicException
     * @group wip
     */
    public function test_get_all_constructor_parameters_works()
    {
        $classname = ClassThatHasSingleConstructorParameter::class;

        $dataType = new \TypeSpec\DataType(
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
     * @covers ::\TypeSpec\get_all_constructor_parameters
     * @throws LogicException
     */
    public function test_get_all_constructor_parameters_errors()
    {
        $classname = ClassThatHasSingleConstructorParameter::class;

        $processedValues = ProcessedValues::fromArray([]);

        $reflection_class = new \ReflectionClass($classname);
        $r_constructor = $reflection_class->getConstructor();
        $constructor_parameters = $r_constructor->getParameters();

        $this->expectException(MissingConstructorParameterNameException::class);
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
