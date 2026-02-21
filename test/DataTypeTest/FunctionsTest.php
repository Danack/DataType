<?php

declare(strict_types=1);

namespace DataTypeTest;

use DataType\DataStorage\DataStorage;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\InputType;
use DataType\Exception\AnnotationClassDoesNotExistExceptionData;
use DataType\Exception\IncorrectNumberOfParametersExceptionData;
use DataType\Exception\MissingClassExceptionData;
use DataType\Exception\MissingConstructorParameterNameExceptionData;
use DataType\Exception\PropertyHasMultipleInputTypeAnnotationsException;
use DataType\Exception\DataTypeDefinitionException;
use DataType\Exception\DataTypeNotImplementedException;
use DataType\Exception\ValidationException;
use DataType\ExtractRule\ExtractRule;
use DataType\ExtractRule\GetInt;
use DataType\ExtractRule\GetString;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\ExtractRule\GetType;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\AlwaysEndsRule;
use DataType\ProcessRule\AlwaysErrorsButDoesntHaltRule;
use DataType\ProcessRule\AlwaysErrorsRule;
use DataType\ProcessRule\ImagickIsRgbColor;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinLength;
use DataType\ValidationResult;
use DataType\Value\Ordering;
use DataTypeTest\Integration\FooErrorsButContinuesParams;
use DataTypeTest\Integration\FooParams;
use DataTypeTest\InputType\Quantity;
use DataTypeTest\DTOTypes\BasicDTO;
use function DataType\array_value_exists;
//use function DataType\check_only_digits;
use function DataType\checkAllowedFormatsAreStrings;
use function DataType\createArrayOfScalarsFromDataStorage;
use function DataType\createArrayOfType;
use function DataType\createArrayOfTypeFromInputStorage;
use function DataType\createArrayOfTypeOrError;
use function DataType\createObjectFromProcessedValues;
use function DataType\createSingleValue;
use function DataType\createSingleValueOrError;
use function DataType\getInputTypeListForClass;
use function DataType\getInputTypesFromAnnotations;
use function DataType\getDefaultSupportedTimeFormats;
use function DataType\getRawCharacters;
use function DataType\getReflectionClassOfAttribute;
use function DataType\normalise_order_parameter;
use function DataType\processInputTypesFromStorage;
use function DataType\processInputTypeWithDataStorage;
use function DataType\processProcessingRules;
use function DataType\processSingleInputType;
use function DataType\validate;
use function DataType\generateOpenApiV300DescriptionForDataType;

/**
 * @coversNothing
 */
class FunctionsTest extends BaseTestCase
{
    public function providesNormaliseOrderParameter()
    {
        return [
            ['foo', 'foo', Ordering::ASC],
            ['+foo', 'foo', Ordering::ASC],
            ['-foo', 'foo', Ordering::DESC],
        ];
    }

    /**
     * @dataProvider providesNormaliseOrderParameter
     * @covers ::DataType\normalise_order_parameter
     */
    public function testNormaliseOrderParameter(string $input, string $expectedName, string $expectedOrder)
    {
        list($name, $order) = normalise_order_parameter($input);

        $this->assertEquals($expectedName, $name);
        $this->assertEquals($expectedOrder, $order);
    }

//    /**
//     * @covers ::DataType\check_only_digits
//     */
//    public function testCheckOnlyDigits()
//    {
//        // An integer gets short circuited
//        $errorMsg = check_only_digits(12345);
//        $this->assertNull($errorMsg);
//
//        // Correct string passes through
//        $errorMsg = check_only_digits('12345');
//        $this->assertNull($errorMsg);
//
//        // Incorrect string passes through
//        $errorMsg = check_only_digits('123X45');
//        $this->assertNotNull($errorMsg);
//
//        // TODO - update string matching.
//        $this->assertStringMatchesFormat("%sposition 3%s", $errorMsg);
//    }

    /**
     * @covers ::DataType\array_value_exists
     */
    public function testArrayValueExists()
    {
        $values = [
            '1',
            '2',
            '3'
        ];

        $foundExactType = array_value_exists($values, '2');
        $this->assertTrue($foundExactType);

        $foundJuggledType = array_value_exists($values, 2);
        $this->assertFalse($foundJuggledType);
    }

    public function provides_getRawCharacters()
    {
        yield ['Hello', '48, 65, 6c, 6c, 6f'];
        yield ["ÁGUEDA", 'c3, 81, 47, 55, 45, 44, 41'];
        yield ["☺😎😋😂", 'e2, 98, ba, f0, 9f, 98, 8e, f0, 9f, 98, 8b, f0, 9f, 98, 82'];
    }

    /**
     * @dataProvider provides_getRawCharacters
     * @covers ::\DataType\getRawCharacters
     * @param string $inputString
     * @param string $expectedOutput
     */
    public function test_getRawCharacters(string $inputString, $expectedOutput)
    {
        $actualOutput = getRawCharacters($inputString);
        $this->assertSame($expectedOutput, $actualOutput);
    }

    /**
     * @covers ::\DataType\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams()
    {
        $name = 'John';
        $age = 34;

        $object = \DataType\createObjectFromProcessedValues(
            \TestObject::class,
            createProcessedValuesFromArray([
                'name' => $name,
                'age' => $age
            ])
        );

        $this->assertInstanceOf(\TestObject::class, $object);
        $this->assertSame($name, $object->getName());
        $this->assertSame($age, $object->getAge());
    }

    /**
     * @covers ::\DataType\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_out_of_order()
    {
        $nameValue = 'John';
        $ageValue = 36;

        $object = \DataType\createObjectFromProcessedValues(
            \TestObject::class,
            createProcessedValuesFromArray([
                'age' => $ageValue,
                'name' => $nameValue
            ])
        );

        $this->assertInstanceOf(\TestObject::class, $object);
        $this->assertSame($ageValue, $object->getAge());
        $this->assertSame($nameValue, $object->getName());
    }

    /**
     * @covers ::\DataType\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_no_constructor()
    {
        $this->expectExceptionMessageMatchesTemplateString(Messages::CLASS_LACKS_CONSTRUCTOR);
        $this->expectException(\DataType\Exception\NoConstructorExceptionData::class);
        createObjectFromProcessedValues(
            \OneColorNoConstructor::class,
            createProcessedValuesFromArray([])
        );
    }

    /**
     * @covers ::\DataType\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_private_constructor()
    {
        $this->expectExceptionMessageMatchesTemplateString(Messages::CLASS_LACKS_PUBLIC_CONSTRUCTOR);
        $this->expectException(\DataType\Exception\NoConstructorExceptionData::class);
        createObjectFromProcessedValues(
            \ThreeColorsPrivateConstructor::class,
            createProcessedValuesFromArray([])
        );
    }

    /**
     * @covers ::\DataType\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_wrong_number_params()
    {
        $this->expectException(IncorrectNumberOfParametersExceptionData::class);
        createObjectFromProcessedValues(
            \NotActuallyAParam::class,
            createProcessedValuesFromArray([])
        );
    }

    /**
     * @covers ::\DataType\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_missing_param()
    {
        $this->expectException(MissingConstructorParameterNameExceptionData::class);
        createObjectFromProcessedValues(
            \NotActuallyAParam::class,
            createProcessedValuesFromArray([
                'name' => 'John',
                'this_is_invalid' => 'Foo'
            ])
        );
    }

    public function provides_getJsonPointerParts()
    {
        yield ['', []];
        yield ['/3', [3]];
        yield ['/', []];
        yield ['/0', [0]];

        yield ['/0/foo', [0, 'foo']];
        yield ['/0/foo/2', [0, 'foo', 2]];
        yield ['/foo', ['foo']];
        yield ['/foo/2', ['foo', 2]];

        yield ['/foo/bar', ['foo', 'bar']];
        yield ['/foo/bar/3', ['foo', 'bar', 3]];
    }

    /**
     * @covers ::\DataType\getInputTypeListForClass
     */
    public function test_getInputParameterListForClass()
    {
        $inputParameters = getInputTypeListForClass(\TestParams::class);
        $this->assertCount(1, $inputParameters);
    }

    /**
     * @covers ::\DataType\getInputTypeListForClass
     */
    public function test_getInputParameterListForClass_missing_class()
    {
        $this->expectException(MissingClassExceptionData::class);
        $inputParameters = getInputTypeListForClass("does_not_exist");
    }

    /**
     * @covers ::\DataType\getInputTypeListForClass
     */
    public function test_getInputParameterListForClass_missing_implements()
    {
        $this->expectException(DataTypeNotImplementedException::class);
        $inputParameters = getInputTypeListForClass(
            \DoesNotImplementInputParameterList::class
        );
    }

    /**
     * @covers ::\DataType\getInputTypeListForClass
     */
    public function test_getInputParameterListForClass_non_inputparameter()
    {
        $this->expectException(DataTypeDefinitionException::class);
        $inputParameters = getInputTypeListForClass(
            \ReturnsBadDataType::class
        );
    }

    /**
     * @covers ::\DataType\processSingleInputType
     */
    public function test_processSingleInputParameter()
    {
        $inputValue = 5;

        $processedValues  = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromArray([
            'foo' => $inputValue,
        ]);

        $param = new Quantity('foo');

        $result = processSingleInputType(
            $param,
            $processedValues,
            $dataStorage
        );

        $this->assertEmpty($result);

        $allValues = $processedValues->getAllValues();
        $this->assertCount(1, $allValues);
        $this->assertSame($inputValue, $allValues['foo']);
    }

    /**
     * @covers ::\DataType\processInputTypesFromStorage
     */
    public function test_processInputParameters()
    {
        $inputParameters = \AlwaysErrorsParams::getInputTypes();
        $dataStorage = TestArrayDataStorage::fromArray([
            'foo' => 'foo string',
            'bar' => 'bar string'
        ]);

        $processedValues  = new ProcessedValues();
        $validationProblems = processInputTypesFromStorage(
            $inputParameters,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblem(
            '/bar',
            \AlwaysErrorsParams::ERROR_MESSAGE,
            $validationProblems
        );
        $this->assertCount(1, $validationProblems);
    }


    /**
     * @covers ::\DataType\processProcessingRules
     */
    public function test_processProcessingRules_works()
    {
        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'bar string'
        ]);
        $dataStorage = $dataStorage->moveKey('bar');

        $processedValues  = new ProcessedValues();
        $minLength = new MinLength(5);
        $message = "forced ending";
        $alwaysEnds = new AlwaysEndsRule($message);
        $alwaysError = new AlwaysErrorsRule('There was error');

        $inputValue = 'Hello world';

        [$validationProblems, $resultValue] = processProcessingRules(
            $inputValue,
            $dataStorage,
            $processedValues,
            $minLength,
            $alwaysEnds,
            $alwaysError
        );

        $this->assertSame($message, $resultValue);
        $this->assertCount(0, $validationProblems);
    }

    /**
     * @covers ::\DataType\processProcessingRules
     */
    public function test_processProcessingRules_errors()
    {
        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'bar string'
        ]);
        $dataStorage = $dataStorage->moveKey('bar');

        $errorMessage = 'There was error';
        $processedValues = new ProcessedValues();
        $alwaysError = new AlwaysErrorsRule($errorMessage);

        $value = 'Hello world';

        [$validationProblems, $value] = processProcessingRules(
            $value,
            $dataStorage,
            $processedValues,
            $alwaysError
        );

        $this->assertNull($value);

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblem(
            '/bar',
            $errorMessage,
            $validationProblems
        );
    }


    /**
     * @covers ::\DataType\createArrayOfTypeFromInputStorage
     */
    public function test_createArrayOfType_works()
    {
        $data = [
            ['name' => 'John 1'],
            ['name' => 'John 2'],
            ['name' => 'John 3'],
        ];

        $dataStorage = TestArrayDataStorage::fromArray($data);
        $getType = GetType::fromClass(\TestParams::class);

        $result = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertFalse($result->anyErrorsFound());

        /** @var array<int, \TestParams> $items */
        $items = $result->getValue();
        $this->assertCount(3, $items);

        $count = 1;

        foreach ($items as $item) {
            $this->assertInstanceOf(\TestParams::class, $item);
            $this->assertSame('John ' . $count, $item->getName());
            $count += 1;
        }
    }


    /**
     * @covers ::\DataType\createArrayOfTypeFromInputStorage
     */
    public function test_createArrayOfType_bad_data()
    {
        $data = [
            ['name' => 'John 1'],
            ['name' => 'John 2'],
            ['name_this_is_typo' => 'John 3'],
        ];

        $dataStorage = TestArrayDataStorage::fromArray($data);
        $getType = GetType::fromClass(\TestParams::class);

        $result = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->anyErrorsFound());

        $this->assertValidationProblem(
            '/2/name',
            Messages::VALUE_NOT_SET,
            $result->getValidationProblems()
        );
    }


    /**
     * @covers ::\DataType\createArrayOfTypeFromInputStorage
     */
    public function test_createArrayOfType_not_array_data()
    {
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 'bar');
        $getType = GetType::fromClass(\TestParams::class);

        $result = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        $this->assertTrue($result->anyErrorsFound());

        $this->assertValidationProblem(
            '/foo',
            Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1,
            $result->getValidationProblems()
        );
        $this->assertCount(1, $result->getValidationProblems());
    }


    /**
     * @covers ::\DataType\processInputTypeWithDataStorage
     */
    public function test_processInputParameter_works()
    {
        $inputParameter = new InputType(
            'bar',
            new GetString()
        );

        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'bar string'
        ]);

        $processedValues  = new ProcessedValues();
        $validationProblems = processInputTypeWithDataStorage(
            $inputParameter,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(0, $validationProblems);

        $this->assertTrue($processedValues->hasValue('bar'));
        $this->assertSame('bar string', $processedValues->getValue('bar'));
    }


    /**
     * @covers ::\DataType\processInputTypeWithDataStorage
     */
    public function test_processInputParameter_errors_on_extract()
    {
        $inputParameter = new InputType(
            'bar',
            new GetInt()
        );

        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'This is not an integer'
        ]);

        $processedValues = new ProcessedValues();
        $validationProblems = processInputTypeWithDataStorage(
            $inputParameter,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblem(
            '/bar',
            Messages::INT_REQUIRED_FOUND_NON_DIGITS2,
            $validationProblems
        );
        $this->assertCount(1, $validationProblems);
    }

    /**
     * @covers ::\DataType\processInputTypeWithDataStorage
     */
    public function test_processInputParameter_extract_ends_processing()
    {
        $value = 12345;

        $extractIsFinal = new class($value) implements ExtractRule  {

            private $value;

            /**
             * @param mixed $value
             */
            public function __construct($value)
            {
                $this->value = $value;
            }

            public function process(
                ProcessedValues $processedValues,
                DataStorage $dataStorage
            ): ValidationResult {
                return ValidationResult::finalValueResult($this->value);
            }

            public function updateParamDescription(ParamDescription $paramDescription): void
            {
                //nothing to do.
            }
        };

        $inputParameter = new InputType(
            'bar',
            $extractIsFinal
        );

        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'hello world'
        ]);

        $processedValues = new ProcessedValues();
        $validationProblems = processInputTypeWithDataStorage(
            $inputParameter,
            $processedValues,
            $dataStorage
        );

        $this->assertEmpty($validationProblems);

        $this->assertTrue($processedValues->hasValue('bar'));
        $this->assertSame($value, $processedValues->getValue('bar'));
    }


    /**
     * @covers ::\DataType\processInputTypeWithDataStorage
     */
    public function test_processInputParameter_errors()
    {
        $errorMessage = "There was error.";

        $inputParameter = new InputType(
            'bar',
            new GetString(),
            new AlwaysErrorsRule($errorMessage)
        );

        $dataStorage = TestArrayDataStorage::fromArray([
            'foo' => 'foo string',
            'bar' => 'bar string'
        ]);

        $processedValues  = new ProcessedValues();
        $validationProblems = processInputTypeWithDataStorage(
            $inputParameter,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblem(
            '/bar',
            $errorMessage,
            $validationProblems
        );
        $this->assertCount(1, $validationProblems);
    }

    /**
     * @covers ::\DataType\createArrayOfTypeOrError
     */
    public function test_createArrayOfTypeOrError()
    {
        $data = [
            ['limit' => 20],
            ['limit' => 30]
        ];

        [$values, $errors] = createArrayOfTypeOrError(
            FooParams::class,
            $data
        );

        $this->assertEmpty($errors);
        $this->assertNotNull($values);

        $this->assertCount(2, $values);

        $this->assertInstanceOf(FooParams::class, $values[0]);
        $this->assertInstanceOf(FooParams::class, $values[1]);

        /** @var FooParams $fooParam1 */
        $fooParam1 = $values[0];
        $this->assertSame(20, $fooParam1->getLimit());

        /** @var FooParams $fooParam2 */
        $fooParam2 = $values[1];
        $this->assertSame(30, $fooParam2->getLimit());
    }

    /**
     * @covers ::\DataType\createArrayOfTypeOrError
     */
    public function testErrors_createArrayOfTypeOrError()
    {
        $data = [
            ['limit' => 20],
            ['limit' => -10]
        ];

        [$values, $validationProblems] = createArrayOfTypeOrError(
            FooParams::class,
            $data
        );

        $this->assertNull($values);
        $this->assertNotNull($validationProblems);
        $this->assertValidationErrorCount(1, $validationProblems);

        /** @var \DataType\ValidationProblem[] $validationProblems */
        $validationProblem = $validationProblems[0];

        $this->assertStringMatchesTemplateString(
            Messages::INT_TOO_SMALL,
            $validationProblem->getProblemMessage()
        );
    }


    /**
     * @covers ::\DataType\createArrayOfTypeOrError
     */
    public function testErrors_createArrayOfTypeOrMultipleError()
    {
        $data = [
            ['limit' => 20],
            ['limit' => -10]
        ];

        [$values, $validationProblems] = createArrayOfTypeOrError(
            FooErrorsButContinuesParams::class,
            $data
        );

        $this->assertNull($values);
        $this->assertNotNull($validationProblems);
        $this->assertValidationErrorCount(3, $validationProblems);

        $this->assertValidationProblemRegexp(
            '/1/limit',
            Messages::INT_TOO_SMALL,
            $validationProblems
        );

        for ($x = 0; $x < 2; $x += 1) {
            $this->assertValidationProblem(
                '/' . $x . '/limit',
                FooErrorsButContinuesParams::MESSAGE,
                $validationProblems
            );
        }
    }

    /**
     * @covers ::\DataType\createArrayOfType
     */
    public function test_createArrayOfType()
    {
        $data = [
            ['limit' => 20],
            ['limit' => 30]
        ];

        $values = createArrayOfType(
            FooParams::class,
            $data
        );

        $this->assertCount(2, $values);

        $this->assertInstanceOf(FooParams::class, $values[0]);
        $this->assertInstanceOf(FooParams::class, $values[1]);

        /** @var FooParams $fooParam1 */
        $fooParam1 = $values[0];
        $this->assertSame(20, $fooParam1->getLimit());

        /** @var FooParams $fooParam2 */
        $fooParam2 = $values[1];
        $this->assertSame(30, $fooParam2->getLimit());
    }


    /**
     * @covers ::\DataType\createArrayOfType
     */
    public function test_createArrayOfTypeErrors()
    {
        $data = [
            ['limit' => 20],
            ['limit' => -30]
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(
            "Validation problems /1/limit Value too small. Min allowed is 0"
        );
        createArrayOfType(
            FooParams::class,
            $data
        );
    }

    /**
     * @covers ::\DataType\checkAllowedFormatsAreStrings
     */
    public function test_checkAllowedFormatsAreStrings()
    {
        $formats = [
            \DateTime::ISO8601,
            \DateTime::RFC2822,
            'D'
        ];

        checkAllowedFormatsAreStrings($formats);

        $bad_formats = [
            \DateTime::ISO8601,
            \DateTime::RFC2822,
            123
        ];

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::ERROR_DATE_FORMAT_MUST_BE_STRING
        );

        // @phpstan-ignore argument.type (intentionally passing bad data to test error handling)
        checkAllowedFormatsAreStrings($bad_formats);
    }

    /**
     * @covers ::\DataType\getInputTypesFromAnnotations
     */
    public function test_getParamsFromAnnotations()
    {
        $inputParameters = getInputTypesFromAnnotations(\ThreeColors::class);
        foreach ($inputParameters as $inputParameter) {
            $this->assertInstanceOf(InputType::class, $inputParameter);
            $this->assertInstanceOf(GetStringOrDefault::class, $inputParameter->getExtractRule());

            $processRules = $inputParameter->getProcessRules();
            $this->assertCount(1, $processRules);
            $processRule = $processRules[0];
            $this->assertInstanceOf(ImagickIsRgbColor::class, $processRule);
        }
    }


    /**
     * @covers ::\DataType\getInputTypesFromAnnotations
     */
    public function test_getParamsFromAnnotations_non_existant_param_class()
    {
        try {
            $inputParameters = getInputTypesFromAnnotations(
                \OneColorWithOtherAnnotationThatDoesNotExist::class
            );
        }
        // @phpstan-ignore catch.neverThrown (exception thrown depends on fixture class)
        catch (AnnotationClassDoesNotExistExceptionData $acdnee) {
            $this->assertStringContainsString(
                'ThisClassDoesNotExistParam', $acdnee->getMessage()
            );
        }
    }

    /**
     * @covers ::\DataType\getInputTypesFromAnnotations
     */
    public function testMultipleParamsErrors()
    {
        try {
            $inputParameters = getInputTypesFromAnnotations(
                \MultipleParamAnnotations::class
            );
        }
        // @phpstan-ignore catch.neverThrown (exception thrown depends on fixture class)
        catch (PropertyHasMultipleInputTypeAnnotationsException $acdnee) {
            $this->assertStringContainsString(
                'background_color',
                $acdnee->getMessage()
            );
        }
    }

    /**
     * @covers ::\DataType\getInputTypesFromAnnotations
     */
    public function test_getParamsFromAnnotations_skips_non_param_annotation()
    {

        $inputParameters = getInputTypesFromAnnotations(
            \OneColorWithOtherAnnotationThatIsNotAParam::class
        );

        $this->assertCount(1, $inputParameters);
        $inputParameter = $inputParameters[0];

        $this->assertSame('background_color', $inputParameter->getName());
    }

    /**
     * @covers ::\DataType\getDefaultSupportedTimeFormats
     */
    public function test_getDefaultSupportedTimeFormats()
    {
        $formats = getDefaultSupportedTimeFormats();
        foreach ($formats as $format) {
            $this->assertIsString($format);
        }
    }

    /**
     * @covers ::\DataType\getReflectionClassOfAttribute
     */
    public function test_getReflectionClassOfAttribute_works()
    {
        $rc = new \ReflectionClass(\ReflectionClassOfAttributeObject::class);

        $refl_property_no_constructor = $rc->getProperty('attribute_exists_no_constructor');
        $attribute = $refl_property_no_constructor->getAttributes()[0];

        $blah = getReflectionClassOfAttribute(
            \ReflectionClassOfAttributeObject::class,
            $attribute->getName(),
            $refl_property_no_constructor
        );

        $this->assertSame(\AttributesExistsNoConstructor::class, $blah->getName());
    }




    /**
     * @covers ::\DataType\getReflectionClassOfAttribute
     */
    public function test_getReflectionClassOfAttribute_attribute_doesnt_exist()
    {
        $rc = new \ReflectionClass(\ReflectionClassOfAttributeObject::class);

        $refl_property_no_constructor = $rc->getProperty('attribute_not_exists');
        $attribute = $refl_property_no_constructor->getAttributes()[0];

        $this->expectException(AnnotationClassDoesNotExistExceptionData::class);
        getReflectionClassOfAttribute(
            \ReflectionClassOfAttributeObject::class,
            $attribute->getName(),
            $refl_property_no_constructor
        );
    }

    /**
     * @covers ::\DataType\createSingleValue
     * @throws ValidationException
     * @throws \DataType\Exception\DataTypeException
     */
    public function testCreateSingleValue()
    {
        $colorInputTypeSpec = new ImagickColorHasInputType(
            'rgb(225, 225, 225)',
            'background_color'
        );

        $inputString = 'red';
        $value = createSingleValue($colorInputTypeSpec, $inputString);
        $this->assertSame($value, $inputString);

        $errorInputString = 'I am not a color.';
        try {
            $value = createSingleValue($colorInputTypeSpec, $errorInputString);
        }
        catch (\DataType\Exception\ValidationException $ve) {
            $this->assertCount(1, $ve->getValidationProblems());
            $this->assertValidationProblemRegexp(
                '/background_color',
                Messages::BAD_COLOR_STRING,
                $ve->getValidationProblems()
            );
        }
    }

    /**
     * @covers ::\DataType\createSingleValueOrError
     * @throws ValidationException
     * @throws \DataType\Exception\DataTypeException
     */
    public function testCreateSingleValueOrError()
    {
        $colorInputTypeSpec = new ImagickColorHasInputType(
            'rgb(225, 225, 225)',
            'background_color'
        );

        $inputString = 'red';
        [$value, $validationErrors] = createSingleValueOrError($colorInputTypeSpec, $inputString);
        $this->assertCount(0, $validationErrors);
        $this->assertSame($value, $inputString);

        $errorInputString = 'I am not a color.';
        [$value, $validationErrors] = createSingleValueOrError($colorInputTypeSpec, $errorInputString);
        $this->assertCount(1, $validationErrors);
        $this->assertNull($value);
    }


    /**
     * @covers ::\DataType\validate
     */
    public function test_validate()
    {
        $dto = new \DataTypeExample\DTOTypes\TestDTO('red', 5);
        [$object, $validationProblems] = validate($dto);

        $this->assertEmpty($validationProblems);
    }

    /**
     * @covers ::\DataType\createArrayOfScalarsFromDataStorage
     */
    public function test_createArrayOfScalarsFromDataStorage_works()
    {
        $expected_array = [5, 2, 3, 4, 5];

        $dataStorage = TestArrayDataStorage::fromArray($expected_array);
        $extract_rule = new GetInt();

        $result = createArrayOfScalarsFromDataStorage(
            $dataStorage,
            $extract_rule,
            []
        );

        $this->assertFalse($result->anyErrorsFound());
        $this->assertEmpty($result->getValidationProblems());
        $this->assertSame($expected_array, $result->getValue());
    }


    /**
     * @return void
     * @throws \DataType\Exception\LogicExceptionData
     */
    public function test_createArrayOfScalarsFromDataStorage_process_error()
    {
        $expected_array = [5, 2, 3, 6, 5];

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArray($expected_array);
        $extract_rule = new GetInt();

        $maxIntRule = new MaxIntValue(5);


        $result = createArrayOfScalarsFromDataStorage(
            $dataStorage,
            $extract_rule,
            [$maxIntRule]
        );


        $this->assertTrue($result->anyErrorsFound());
        $this->assertNull($result->getValue());
        $this->assertCount(1, $result->getValidationProblems());

        $this->assertValidationProblemRegexp(
            '/3',
            Messages::INT_TOO_LARGE,
            $result->getValidationProblems()
        );
    }




    /**
     * @covers ::\DataType\createArrayOfScalarsFromDataStorage
     * @return void
     * @throws \DataType\Exception\LogicExceptionData
     */
    public function test_createArrayOfScalarsFromDataStorage_process_error_twice()
    {
        $expected_array = [6, 1, 2, 3, 5];

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArray($expected_array);
        $extract_rule = new GetInt();

        $error_message = "Why must you always fail me?";

        $maxIntRule = new MaxIntValue(5);
        $error_without_halting = new AlwaysErrorsButDoesntHaltRule($error_message);

        $result = createArrayOfScalarsFromDataStorage(
            $dataStorage,
            $extract_rule,
            [
                $error_without_halting,
                $maxIntRule,
            ]
        );

        $this->assertTrue($result->anyErrorsFound());
        $this->assertNull($result->getValue());
        $this->assertCount(6, $result->getValidationProblems());

        $validationProblems = $result->getValidationProblems();

        $this->assertValidationProblemRegexp(
            '/0',
            Messages::INT_TOO_LARGE,
            $validationProblems
        );

        for ($x = 3; $x < 5; $x += 1) {
            $this->assertValidationProblem(
                '/' . $x,
                $error_message,
                $validationProblems
            );
        }
    }




    /**
     * @covers ::\DataType\createArrayOfScalarsFromDataStorage
     */
    public function test_createArrayOfScalarsFromDataStorage_errors_not_array()
    {
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition(
            'foo',
            'bar'
        );
        $extract_rule = new GetInt();

        $result = createArrayOfScalarsFromDataStorage(
            $dataStorage,
            $extract_rule,
            []
        );

        $this->assertTrue($result->isFinalResult());
        $validationProblems = $result->getValidationProblems();
        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_MESSAGE_NOT_ARRAY,
            $validationProblems
        );
    }

    /**
     * @covers ::\DataType\createArrayOfScalarsFromDataStorage
     */
    public function testMissingGivesError()
    {
        $extract_rule = new GetInt();
        $dataStorage = TestArrayDataStorage::createMissing('foo');

        $result = createArrayOfScalarsFromDataStorage(
            $dataStorage,
            $extract_rule,
            []
        );

        $this->assertProblems(
            $result,
            ['/foo' => Messages::ERROR_MESSAGE_NOT_SET]
        );
    }


    /**
     * @covers ::\DataType\generateOpenApiV300DescriptionForDataType
     */
    public function test_generateOpenApiV300DescriptionForDataType()
    {
        $result = generateOpenApiV300DescriptionForDataType(BasicDTO::class);

        $expected = array (
            0 =>
                array (
                    'name' => 'color',
                    'required' => false,
                    'schema' =>
                        array (
                            'default' => 'blue',
                            'type' => 'string',
                            'enum' =>
                                array (
                                    0 => 'red',
                                    1 => 'green',
                                    2 => 'blue',
                                ),
                        ),
                ),
            1 =>
                array (
                    'name' => 'quantity',
                    'required' => true,
                    'schema' =>
                        array (
                            'minimum' => 1,
                            'maximum' => 20,
                            'type' => 'integer',
                            'exclusiveMaximum' => false,
                            'exclusiveMinimum' => false,
                        ),
                ),
        );

        $this->assertSame($expected, $result);
    }

    /**
     * @covers ::\DataType\generateOpenApiV300DescriptionForDataType
     */
    public function test_generateOpenApiV300DescriptionForDataType_errors()
    {
        $this->expectException(\DataType\Exception\DataTypeNotImplementedException::class);
        /** @phpstan-ignore argument.type (deliberately passing non-DataType class to test exception) */
        generateOpenApiV300DescriptionForDataType(\stdClass::class);
    }
}
