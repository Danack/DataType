<?php

declare(strict_types=1);

namespace DataTypeTest\OpenApi;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Exception\OpenApiExceptionData;
use DataType\ExtractRule\GetInt;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\ExtractRule\GetOptionalInt;
use DataType\ExtractRule\GetOptionalString;
use DataType\ExtractRule\GetString;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\InputType;
use DataType\OpenApi\ItemsObject;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\OpenApi\ShouldNeverBeCalledParamDescription;
use DataType\ProcessRule\AlwaysEndsRule;
use DataType\ProcessRule\Enum;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinIntValue;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\NullIfEmpty;
use DataType\ProcessRule\PositiveInt;
use DataType\ProcessRule\Trim;
use DataType\ProcessRule\ValidDate;
use DataType\ProcessRule\ValidDatetime;
use DataTypeTest\BaseTestCase;
use DataTypeTestFixture\OpenApi\RequiredStringExample;

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
        /** @var array<int, InputType> $rules */
        $rules = RequiredStringExample::getInputParameterList();
        $this->performFullTest([], $descriptionExpectations, $rules);
    }

    public function testMinLength()
    {
        $schemaExpectations = [
            'minLength' => RequiredStringExample::MIN_LENGTH,
        ];

        /** @var array<int, InputType> $rules */
        $rules = RequiredStringExample::getInputParameterList();
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testMaxLength()
    {
        $schemaExpectations = [
            'maxLength' => RequiredStringExample::MAX_LENGTH,
        ];

        /** @var array<int, InputType> $rules */
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

    public static function providesValidMinimumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @param int $minLength
     */
    #[DataProvider('providesValidMinimumLength')]
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

    public static function providesInvalidMininumLength()
    {
        return [[0], [-1], [-2], [-3] ];
    }

    /**
     * @param int $minLength
     */
    #[DataProvider('providesInvalidMininumLength')]
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


    public static function providesInvalidMaximumLength()
    {
        return [[0], [-1] ];
    }

    /**
     * @param int $maxLength
     */
    #[DataProvider('providesInvalidMaximumLength')]
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

    public static function providesValidMaximumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @param int $maxLength
     */
    #[DataProvider('providesValidMaximumLength')]
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
     * @param array<string, mixed> $schemaExpectations
     * @param array<int, InputType> $rules
     * @throws OpenApiExceptionData
     */
    private function performSchemaTest($schemaExpectations, $rules)
    {
        /** @var array<int, array<string, mixed>> $paramDescription */
        $paramDescription = OpenApiV300ParamDescription::createFromInputTypes($rules);

        $this->assertCount(1, $paramDescription);
        /** @var array<string, mixed> $statusDescription */
        $statusDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $statusDescription);
        /** @var array<string, mixed> $schema */
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


    /**
     * @param array<string, mixed> $schemaExpectations
     * @param array<string, mixed> $paramExpectations
     * @param array<int, InputType> $rules
     */
    private function performFullTest($schemaExpectations, $paramExpectations, $rules)
    {
        /** @var array<int, array<string, mixed>> $paramDescription */
        $paramDescription = OpenApiV300ParamDescription::createFromInputTypes($rules);

        $this->assertCount(1, $paramDescription);
        /** @var array<string, mixed> $openApiDescription */
        $openApiDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $openApiDescription);
        /** @var array<string, mixed> $schema */
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

    public function testPatternAndNullableAreIncludedInSchema(): void
    {
        $description = new OpenApiV300ParamDescription('John');
        $description->setPattern('^foo$');
        $description->setNullAllowed(true);

        /** @var array{name: string, schema: array{pattern: string, nullable: bool}} $result */
        $result = $description->toArray();

        $this->assertSame('^foo$', $result['schema']['pattern']);
        $this->assertTrue($result['schema']['nullable']);
    }

    public function testGetNameReturnsCurrentName(): void
    {
        $description = new OpenApiV300ParamDescription('John');

        $this->assertSame('John', $description->getName());
    }

    public function testSetMaxItemsStoresValue(): void
    {
        $description = new OpenApiV300ParamDescription('John');
        $description->setMaxItems(7);

        $this->assertSame(7, $description->getMaxItems());
    }

    public function testSetInThrowsNotImplementedException(): void
    {
        $description = new OpenApiV300ParamDescription('John');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage('setIn not implemented yet.');
        $description->setIn('query');
    }

    public function testSetSchemaThrowsNotImplementedException(): void
    {
        $description = new OpenApiV300ParamDescription('John');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage('setSchema not implemented yet.');
        $description->setSchema('schema');
    }

    public function testSetAllowEmptyValueThrowsNotImplementedException(): void
    {
        $description = new OpenApiV300ParamDescription('John');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage('setAllowEmptyValue not implemented yet.');
        $description->setAllowEmptyValue(true);
    }

    public function testGetItemsThrowsNotImplementedException(): void
    {
        $description = new OpenApiV300ParamDescription('John');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage('getItems not implemented yet.');
        $description->getItems();
    }

    public function testSetItemsThrowsNotImplementedException(): void
    {
        $description = new OpenApiV300ParamDescription('John');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage('setItems not implemented yet.');
        $description->setItems(self::createItemsObject());
    }

    public function testSetUniqueItemsThrowsNotImplementedException(): void
    {
        $description = new OpenApiV300ParamDescription('John');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage('setUniqueItems not implemented yet.');
        $description->setUniqueItems(true);
    }

    public function testSetMultipleOfThrowsNotImplementedException(): void
    {
        $description = new OpenApiV300ParamDescription('John');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage('setMultipleOf not implemented yet.');
        $description->setMultipleOf(5);
    }

    public function testSetTypeThrowsForUnknownType(): void
    {
        $description = new OpenApiV300ParamDescription('John');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage("Type [nonsense] is not known for the OpenApi spec.");
        $description->setType('nonsense');
    }

    public function testSetFormatThrowsForUnknownNumberFormat(): void
    {
        $description = new OpenApiV300ParamDescription('John');
        $description->setType('number');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage("Format [bogus] is not known for type 'number' the OpenApi spec.");
        $description->setFormat('bogus');
    }

    public function testSetFormatWorksForIntegerType(): void
    {
        $description = new OpenApiV300ParamDescription('John');
        $description->setType('integer');
        $description->setFormat('int32');

        $this->assertSame('int32', $description->getFormat());
    }

    public function testSetFormatThrowsForUnknownIntegerFormat(): void
    {
        $description = new OpenApiV300ParamDescription('John');
        $description->setType('integer');

        $this->expectException(OpenApiExceptionData::class);
        $this->expectExceptionMessage("Format [bogus] is not known for type 'integer' the OpenApi spec.");
        $description->setFormat('bogus');
    }

    private static function createItemsObject(): ItemsObject
    {
        return new class implements ItemsObject {
            public function setType(string $type): void
            {
            }

            public function setFormat(string $format): void
            {
            }

            public function setItems(string $items): void
            {
            }

            public function setMaximum($maximum): void
            {
            }

            public function setExclusiveMaximum(bool $exclusiveMinimum): void
            {
            }

            public static function setMinimum($number): void
            {
            }

            public function setExclusiveMinimum(bool $exclusiveMinimum): void
            {
            }

            public function setMaxLength(int $maxLength): void
            {
            }

            public function setMinLength(int $minLength): void
            {
            }

            public function setPattern(string $pattern): void
            {
            }

            public function setMaxItems(int $maxItems): void
            {
            }

            public function setMinItems(int $minItems): void
            {
            }

            public function setUniqueItems(bool $uniqueItems): void
            {
            }

            public function setEnum(array $enum): void
            {
            }

            public function setMultipleOf($number): void
            {
            }
        };
    }
}
