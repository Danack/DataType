<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\InputStorage\ArrayInputStorage;
use Params\InputStorage\InputStorage;
use Params\Exception\InputParameterListException;
use Params\Exception\TypeNotInputParameterListException;
use Params\ExtractRule\ExtractRule;
use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetString;
use Params\InputParameter;
use Params\Messages;
use Params\ProcessedValues;
use Params\ProcessRule\AlwaysEndsRule;
use Params\ProcessRule\MinLength;
use Params\OpenApi\ParamDescription;
use Params\Value\Ordering;
use Params\Exception\MissingClassException;
use Params\ProcessRule\AlwaysErrorsRule;
use Params\ExtractRule\GetType;
use Params\ValidationResult;
use function Params\unescapeJsonPointer;
use function Params\array_value_exists;
use function Params\check_only_digits;
use function Params\normalise_order_parameter;
use function Params\escapeJsonPointer;
use function Params\getRawCharacters;
use function Params\getInputParameterListForClass;
use function Params\processInputParameters;
use function Params\processInputParameter;
use function Params\processProcessingRules;
use function Params\createArrayOfTypeFromInputStorage;
use function Params\createArrayOfType;
use function Params\createArrayOfTypeOrError;
use ParamsTest\Integration\FooParams;

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
     * @covers ::Params\normalise_order_parameter
     */
    public function testNormaliseOrderParameter($input, $expectedName, $expectedOrder)
    {
        list($name, $order) = normalise_order_parameter($input);

        $this->assertEquals($expectedName, $name);
        $this->assertEquals($expectedOrder, $order);
    }

    /**
     * @covers ::Params\check_only_digits
     */
    public function testCheckOnlyDigits()
    {
        // An integer gets short circuited
        $errorMsg = check_only_digits(12345);
        $this->assertNull($errorMsg);

        // Correct string passes through
        $errorMsg = check_only_digits('12345');
        $this->assertNull($errorMsg);

        // Incorrect string passes through
        $errorMsg = check_only_digits('123X45');

        // TODO - update string matching.
        $this->assertStringMatchesFormat("%sposition 3%s", $errorMsg);
    }

    /**
     * @covers ::Params\array_value_exists
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


    public function providesEscapeJsonPointer()
    {
        return [

            ["a/b", "a~1b"],
            ["m~n", "m~0n"],

            ["~/0", "~0~10"],
            ["~/2", "~0~12"],
        ];
    }


    /**
     * @dataProvider providesEscapeJsonPointer
     * @covers ::\Params\escapeJsonPointer
     */
    public function testEscapeJsonPointer($unescaped, $expectedEscaped)
    {
        $actualEscaped = escapeJsonPointer($unescaped);
        $this->assertSame($expectedEscaped, $actualEscaped);
    }

    /**
     * @dataProvider providesEscapeJsonPointer
     * @covers ::Params\unescapeJsonPointer
     */
    public function testUnescapeJsonPointer($expectedUnescaped, $escaped)
    {
        $actualUnescaped = unescapeJsonPointer($escaped);
        $this->assertSame($expectedUnescaped, $actualUnescaped);
    }

//    /**
//     * @covers \Params\Functions::addChildErrorMessagesForArray
//     */
//    public function testaddChildErrorMessagesForArray()
//    {
//        $name = 'foo';
//        $message = 'Something went wrong.';
//        $problems = [
//            '/bar' => $message
//        ];
//
//        $problems = Functions::addChildErrorMessagesForArray(
//            $name,
//            $problems,
//            []
//        );
//
//        $expectedResult = [
//            '/foo/bar' => $message
//        ];
//
//        $this->assertSame($expectedResult, $problems);
//    }

    public function provides_getRawCharacters()
    {
        yield ['Hello', '48, 65, 6c, 6c, 6f'];
        yield ["ÁGUEDA", 'c3, 81, 47, 55, 45, 44, 41'];
        yield ["☺😎😋😂", 'e2, 98, ba, f0, 9f, 98, 8e, f0, 9f, 98, 8b, f0, 9f, 98, 82'];
    }

    /**
     * @dataProvider provides_getRawCharacters
     * @covers ::\Params\getRawCharacters
     * @param string $inputString
     * @param $expectedOutput
     */
    public function test_getRawCharacters(string $inputString, $expectedOutput)
    {
        $actualOutput = getRawCharacters($inputString);
        $this->assertSame($expectedOutput, $actualOutput);
    }

    /**
     * @covers ::\Params\createObjectFromParams
     */
    public function test_CreateObjectFromParams()
    {
        $fooValue = 'John';
        $barValue = 123;

        $object = \Params\createObjectFromParams(
            \TestObject::class,
            [
                'foo' => $fooValue,
                'bar' => $barValue
            ]
        );

        $this->assertInstanceOf(\TestObject::class, $object);
        $this->assertSame($fooValue, $object->getFoo());
        $this->assertSame($barValue, $object->getBar());
    }


    public function provides_getJsonPointerParts()
    {
        yield ['/[3]', [3]];
        yield ['/', []];
        yield ['/[0]', [0]];

        yield ['/[0]/foo', [0, 'foo']];
        yield ['/[0]/foo[2]', [0, 'foo', 2]];
        yield ['/foo', ['foo']];
        yield ['/foo[2]', ['foo', 2]];

        yield ['/foo/bar', ['foo', 'bar']];
        yield ['/foo/bar[3]', ['foo', 'bar', 3]];
    }

    /**
     * @dataProvider provides_getJsonPointerParts
     * @covers ::\Params\getJsonPointerParts
     * @param $input
     * @param $expected
     */
    public function test_getJsonPointerParts($input, $expected)
    {
        $message = "We should move to actually support json pointer correctly to make it easier to implement";
        $message .= "Also, I can't remember what the correct behaviour is meant to be here.";

        $this->markTestSkipped($message);
        $actual = \Params\getJsonPointerParts($input);
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::\Params\getInputParameterListForClass
     */
    public function test_getInputParameterListForClass()
    {
        $inputParameters = getInputParameterListForClass(\TestParams::class);
        $this->assertCount(1, $inputParameters);
    }

    /**
     * @covers ::\Params\getInputParameterListForClass
     */
    public function test_getInputParameterListForClass_missing_class()
    {
        $this->expectException(MissingClassException::class);
        $inputParameters = getInputParameterListForClass("does_not_exist");
    }

    /**
     * @covers ::\Params\getInputParameterListForClass
     */
    public function test_getInputParameterListForClass_missing_implements()
    {
        $this->expectException(TypeNotInputParameterListException::class);
        $inputParameters = getInputParameterListForClass(
            \DoesNotImplementInputParameterList::class
        );
    }

    /**
     * @covers ::\Params\getInputParameterListForClass
     */
    public function test_getInputParameterListForClass_non_inputparameter()
    {
        $this->expectException(InputParameterListException::class);
        $inputParameters = getInputParameterListForClass(
            \ReturnsBadInputParameterList::class
        );
    }

    /**
     * @covers ::\Params\processInputParameters
     */
    public function test_processInputParameters()
    {
        $inputParameters = \AlwaysErrorsParams::getInputParameterList();
        $dataStorage = ArrayInputStorage::fromArray([
            'foo' => 'foo string',
            'bar' => 'bar string'
        ]);

        $paramValues  = new ProcessedValues();
        $validationProblems = processInputParameters(
            $inputParameters,
            $paramValues,
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
     * @covers ::\Params\processProcessingRules
     */
    public function test_processProcessingRules_works()
    {
        $dataStorage = ArrayInputStorage::fromArray([
            'bar' => 'bar string'
        ]);
        $dataStorage = $dataStorage->moveKey('bar');

        $paramValues  = new ProcessedValues();
        $minLength = new MinLength(5);
        $message = "forced ending";
        $alwaysEnds = new AlwaysEndsRule($message);
        $alwaysError = new AlwaysErrorsRule('There was error');

        $inputValue = 'Hello world';

        [$validationProblems, $resultValue] = processProcessingRules(
            $inputValue,
            $dataStorage,
            $paramValues,
            $minLength,
            $alwaysEnds,
            $alwaysError
        );

        $this->assertSame($message, $resultValue);
        $this->assertCount(0, $validationProblems);
    }

    /**
     * @covers ::\Params\processProcessingRules
     */
    public function test_processProcessingRules_errors()
    {
        $dataStorage = ArrayInputStorage::fromArray([
            'bar' => 'bar string'
        ]);
        $dataStorage = $dataStorage->moveKey('bar');

        $errorMessage = 'There was error';
        $paramValues  = new ProcessedValues();
        $alwaysError = new AlwaysErrorsRule($errorMessage);

        $value = 'Hello world';

        [$validationProblems, $value] = processProcessingRules(
            $value,
            $dataStorage,
            $paramValues,
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
     * @covers ::\Params\createArrayOfType
     */
    public function test_createArrayOfType_works()
    {
        $data = [
            ['name' => 'John 1'],
            ['name' => 'John 2'],
            ['name' => 'John 3'],
        ];

        $dataStorage = ArrayInputStorage::fromArray($data);
        $getType = GetType::fromClass(\TestParams::class);

        $result = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertFalse($result->anyErrorsFound());

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
     * @covers ::\Params\createArrayOfType
     */
    public function test_createArrayOfType_bad_data()
    {
        $data = [
            ['name' => 'John 1'],
            ['name' => 'John 2'],
            ['name_this_is_typo' => 'John 3'],
        ];

        $dataStorage = ArrayInputStorage::fromArray($data);
        $getType = GetType::fromClass(\TestParams::class);

        $result = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->anyErrorsFound());

        $this->assertValidationProblem(
            '/[2]/name',
            Messages::VALUE_NOT_SET,
            $result->getValidationProblems()
        );
    }


    /**
     * @covers ::\Params\createArrayOfType
     */
    public function test_createArrayOfType_not_array_data()
    {
        $dataStorage = ArrayInputStorage::fromSingleValue('foo', 'bar');
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
     * @covers ::\Params\processInputParameter
     */
    public function test_processInputParameter_works()
    {
        $inputParameter = new InputParameter(
            'bar',
            new GetString()
        );

        $dataStorage = ArrayInputStorage::fromArray([
            'bar' => 'bar string'
        ]);

        $paramValues  = new ProcessedValues();
        $validationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
            $dataStorage
        );

        $this->assertCount(0, $validationProblems);

        $this->assertTrue($paramValues->hasValue('bar'));
        $this->assertSame('bar string', $paramValues->getValue('bar'));
    }


    /**
     * @covers ::\Params\processInputParameter
     */
    public function test_processInputParameter_errors_on_extract()
    {

        $inputParameter = new InputParameter(
            'bar',
            new GetInt()
        );

        $dataStorage = ArrayInputStorage::fromArray([
            'bar' => 'This is not an integer'
        ]);

        $paramValues = new ProcessedValues();
        $validationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
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
     * @covers ::\Params\processInputParameter
     */
    public function test_processInputParameter_extract_ends_processing()
    {
        $value = 12345;

        $extractIsFinal = new class($value) implements ExtractRule  {

            private $value;

            public function __construct($value)
            {
                $this->value = $value;
            }

            public function process(
                ProcessedValues $processedValues,
                InputStorage $dataLocator
            ): ValidationResult {
                return ValidationResult::finalValueResult($this->value);
            }

            public function updateParamDescription(ParamDescription $paramDescription): void
            {
                //nothing to do.
            }
        };

        $inputParameter = new InputParameter(
            'bar',
            $extractIsFinal
        );

        $dataStorage = ArrayInputStorage::fromArray([
            'bar' => 'hello world'
        ]);

        $paramValues = new ProcessedValues();
        $validationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
            $dataStorage
        );

        $this->assertEmpty($validationProblems);

        $this->assertTrue($paramValues->hasValue('bar'));
        $this->assertSame($value, $paramValues->getValue('bar'));
    }


    /**
     * @covers ::\Params\processInputParameter
     */
    public function test_processInputParameter_errors()
    {
        $errorMessage = "There was error.";

        $inputParameter = new InputParameter(
            'bar',
            new GetString(),
            new AlwaysErrorsRule($errorMessage)
        );

        $dataStorage = ArrayInputStorage::fromArray([
            'foo' => 'foo string',
            'bar' => 'bar string'
        ]);

        $paramValues  = new ProcessedValues();
        $validationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
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
     * @group wip
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

        $this->assertCount(2, $values);

        $this->assertInstanceOf(FooParams::class, $values[0]);
        $this->assertInstanceOf(FooParams::class, $values[1]);

        /** @var $fooParam1 FooParams */
        $fooParam1 = $values[0];
        $this->assertSame(20, $fooParam1->getLimit());

        /** @var $fooParam2 FooParams */
        $fooParam2 = $values[1];
        $this->assertSame(30, $fooParam2->getLimit());
    }



    /**
     * @group wip
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

        /** @var $fooParam1 FooParams */
        $fooParam1 = $values[0];
        $this->assertSame(20, $fooParam1->getLimit());

        /** @var $fooParam2 FooParams */
        $fooParam2 = $values[1];
        $this->assertSame(30, $fooParam2->getLimit());
    }
}
