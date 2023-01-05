<?php

declare(strict_types=1);

namespace DataTypeTest\OpenApi;

use DataType\InputType;
use DataType\OpenApi\ShouldNeverBeCalledParamDescription;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\ProcessRule\Enum;
use DataType\ExtractRule\GetInt;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\ExtractRule\GetOptionalInt;
use DataType\ExtractRule\GetOptionalString;
use DataType\ExtractRule\GetString;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinIntValue;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\PositiveInt;
use DataType\ProcessRule\Trim;
use DataType\ProcessRule\ValidDate;
use DataType\ProcessRule\ValidDatetime;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\AlwaysEndsRule;
use DataType\Exception\OpenApiExceptionData;
use DataType\ProcessRule\NullIfEmpty;

/**
 * @coversNothing
 */
class DescriptionTest extends BaseTestCase
{
    public function testEnum()
    {
        $values = [
            'available',
            'pending',
            'sold'
        ];
        $schemaExpectations = [
            'enum' => $values,
        ];

        $rules =  [
            new InputType(
                'value',
                new GetString(),
                new Enum($values)
            ),
        ];
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testRequired()
    {
        $descriptionExpectations = [
            'required' => true,
        ];
        $rules = RequiredStringExample::getInputParameterList();
        $this->performFullTest([], $descriptionExpectations, $rules);
    }

    public function testMinLength()
    {
        $schemaExpectations = [
            'minLength' => RequiredStringExample::MIN_LENGTH,
        ];

        $rules = RequiredStringExample::getInputParameterList();
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testMaxLength()
    {
        $schemaExpectations = [
            'maxLength' => RequiredStringExample::MAX_LENGTH,
        ];

        $rules = RequiredStringExample::getInputParameterList();
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testInt()
    {
        $descriptionExpectations = [
            'required' => true
        ];

        $schemaExpectations = [
            'type' => 'integer'
        ];

        $rules = [
            new InputType(
                'value',
                new GetInt()
            ),
        ];

        $this->performFullTest($schemaExpectations, $descriptionExpectations, $rules);
    }

    public function testIntOrDefault()
    {
        $default = 5;
        $schemaExpectations = [
            'type' => 'integer',
            'default' => $default
        ];
        $paramExpectations = [
            'required' => false,
        ];

        $rules = [
            new InputType(
                'value',
                new GetIntOrDefault($default)
            ),
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testStringOrDefault()
    {
        $default = 'foo';
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'string',
            'default' => $default
        ];

        $rules = [
            new InputType(
                'value',
                new GetStringOrDefault($default)
            ),
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testOptionalInt()
    {
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'integer'
        ];

        $rules = [
            new InputType(
                'value',
                new GetOptionalInt()
            ),
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testOptionalString()
    {
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'string'
        ];

        $rules = [
            new InputType(
                'value',
                new GetOptionalString()
            ),
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testMinInt()
    {
        $maxValue = 10;
        $schemaExpectations = [
            'minimum' => $maxValue,
            'exclusiveMinimum' => false
        ];

        $rules = [
            new InputType(
                'value',
                new GetInt(),
                new MinIntValue($maxValue)
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testMaximumLength()
    {
        $maxLength = 10;
        $schemaExpectations = [
            'maxLength' => $maxLength,
        ];

        $rules = [
            new InputType(
                'value',
                new GetString(),
                new MaxLength($maxLength)
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function providesValidMinimumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @dataProvider providesValidMinimumLength
     */
    public function testMininumLength($minLength)
    {
        $schemaExpectations = [
            'minLength' => $minLength,
        ];

        $rules = [
            new InputType(
                'value',
                new GetString(),
                new MinLength($minLength)
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function providesInvalidMininumLength()
    {
        return [[0], [-1], [-2], [-3] ];
    }

    /**
     * @param $minLength
     * @dataProvider providesInvalidMininumLength
     */
    public function testInvalidMininumLength($minLength)
    {
        $rules = [
            new InputType(
                'value',
                new GetString(),
                new MinLength($minLength)
            ),
        ];

        $this->expectException(OpenApiExceptionData::class);
        OpenApiV300ParamDescription::createFromInputTypes($rules);
    }


    public function providesInvalidMaximumLength()
    {
        return [[0], [-1] ];
    }

    /**
     * @param $maxLength
     * @dataProvider providesInvalidMaximumLength
     */
    public function testInvalidMaximumLength($maxLength)
    {
        $rules = [
            new InputType(
                'value',
                new GetString(),
                new MaxLength($maxLength)
            ),
        ];

        $this->expectException(OpenApiExceptionData::class);
        OpenApiV300ParamDescription::createFromInputTypes($rules);
    }

    public function providesValidMaximumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @param $maxLength
     * @dataProvider providesValidMaximumLength
     */
    public function testValidMaximumLength($maxLength)
    {
        $rules = [
            new InputType(
                'value',
                new GetString(),
                new MaxLength($maxLength)
            ),
        ];

        $schemaExpectations = [
            'maxLength' => $maxLength,
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testEmptySchema()
    {
        $description = new OpenApiV300ParamDescription('John');
        $description->setName('testing');
        $result = $description->toArray();
        $this->assertEquals(['name' => 'testing'], $result);
    }

    public function testMaxInt()
    {
        $maxValue = 45;
        $schemaExpectations = [
            'maximum' => $maxValue,
            'exclusiveMaximum' => false
        ];

        $rules = [
            new InputType(
                'value',
                new GetInt(),
                new MaxIntValue($maxValue)
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }


    public function testPositiveInt()
    {
        $schemaExpectations = [
            'minimum' => 0,
            'exclusiveMinimum' => false,
            'type' => 'integer'
        ];

        $rules = [
            new InputType(
                'value',
                new GetInt(),
                new PositiveInt()
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

//    public function testSkipIfNull()
//    {
//        $schemaExpectations = [
//            'nullable' => true
//        ];
//        $rules = [
//            new InputParameter(
//                'value',
//                new GetStringOrDefault(null),
//                new SkipIfNull()
//            ),
//        ];
//
//        $this->performSchemaTest($schemaExpectations, $rules);
//    }

    public function testValidDate()
    {
        $schemaExpectations = [
            'type' => 'string',
            'format' => 'date'
        ];
        $rules = [
            new InputType(
                'value',
                new GetString(),
                new ValidDate()
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testValidDateTime()
    {
        $schemaExpectations = [
            'type' => 'string',
            'format' => 'date-time'
        ];
        $rules = [
            new InputType(
                'value',
                new GetString(),
                new ValidDatetime()
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }


    /**
     * @param $schemaExpectations
     * @param InputType[] $rules
     * @throws OpenApiExceptionData
 */
    private function performSchemaTest($schemaExpectations, $rules)
    {
        $paramDescription = OpenApiV300ParamDescription::createFromInputTypes($rules);

        $this->assertCount(1, $paramDescription);
        $statusDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $statusDescription);
        $schema = $statusDescription['schema'];

        foreach ($schemaExpectations as $key => $value) {
            $this->assertArrayHasKey(
                $key,
                $schema,
                "Schema missing key [$key]. Schema is " .json_encode($schema)
            );
            $this->assertEquals($value, $schema[$key]);
        }
    }


    private function performFullTest($schemaExpectations, $paramExpectations, $rules)
    {
        $paramDescription = OpenApiV300ParamDescription::createFromInputTypes($rules);

        $this->assertCount(1, $paramDescription);
        $openApiDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $openApiDescription);
        $schema = $openApiDescription['schema'];

        foreach ($schemaExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $schema, "Schema missing key [$key]. Schema is " .json_encode($schema));
            $this->assertEquals($value, $schema[$key]);
        }

        foreach ($paramExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $openApiDescription, "openApiDescription missing key [$key]. Description is " .json_encode($openApiDescription));
            $this->assertEquals($value, $openApiDescription[$key]);
        }
    }

    public function testStringIntEnumAllowed()
    {
        $description = new OpenApiV300ParamDescription('John');
        $description->setEnum(['foo', 5]);
    }


    public function testNonStringNonIntEnumThrows()
    {
        $description = new OpenApiV300ParamDescription('John');
        $this->expectException(OpenApiExceptionData::class);
        $description->setEnum(['foo', [123, 456]]);
    }

    /**
     *
     */
    public function testCoverageOnly()
    {
        $description = new ShouldNeverBeCalledParamDescription();
        $trimRule = new Trim();
        $trimRule->updateParamDescription($description);

        $alwaysEndsRule = new AlwaysEndsRule(5);
        $alwaysEndsRule->updateParamDescription($description);
    }

    /**
     * @covers \DataType\ProcessRule\NullIfEmpty
     */
    public function testNullIfEmpty()
    {
        $rule = new NullIfEmpty();

        $description = new OpenApiV300ParamDescription('John');
        $rule->updateParamDescription($description);
        $this->assertTrue($description->getNullAllowed());
    }
}
