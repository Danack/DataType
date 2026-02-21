<?php

namespace DataType;

// Hi there.
//
// This file contains functions that are used internally by the DataType
// library. If you think you need to use one, feel free to do so, but also
// let us know why, and maybe we'll move it to the public functions.php file.

use DataType\DataStorage\ArrayDataStorage;
use DataType\DataStorage\DataStorage;
use DataType\Exception\AnnotationClassDoesNotExistExceptionData;
use DataType\Exception\IncorrectNumberOfParametersExceptionData;
use DataType\Exception\InvalidDatetimeFormatExceptionData;
use DataType\ExtractRule\ExtractRule;
use DataType\Exception\LogicExceptionData;
use DataType\Exception\MissingClassExceptionData;
use DataType\Exception\MissingConstructorParameterNameExceptionData;
use DataType\Exception\NoConstructorExceptionData;
use DataType\Exception\PropertyHasMultipleInputTypeAnnotationsException;
use DataType\Exception\DataTypeDefinitionException;
use DataType\Exception\DataTypeNotImplementedException;
use DataType\Exception\ValidationException;
use DataType\ExtractRule\GetType;
use DataType\Exception\JsonDecodeException;
use DataType\Exception\JsonEncodeException;
use DataType\ProcessRule\ProcessRule;
use DataType\Value\Ordering;

/**
 * @template T of object
 * @param class-string<T> $classname
 * @param ProcessedValues $processedValues
 * @return T
 * @throws \ReflectionException
 * @throws NoConstructorExceptionData
 */
function createObjectFromProcessedValues(string $classname, ProcessedValues $processedValues)
{
    $reflection_class = new \ReflectionClass($classname);

    $r_constructor = $reflection_class->getConstructor();

    // No constructor, can't create the object with parameters.
    // Yes this is an arbitrary choice, but it seems sensible.
    if ($r_constructor === null) {
        throw NoConstructorExceptionData::noConstructor($classname);
    }

    if ($r_constructor->isPublic() !== true) {
        throw NoConstructorExceptionData::notPublicConstructor($classname);
    }

    $constructor_parameters = $r_constructor->getParameters();
    if (count($constructor_parameters) !== $processedValues->getCount()) {
        throw IncorrectNumberOfParametersExceptionData::wrongNumber(
            $classname,
            count($constructor_parameters),
            $processedValues->getCount()
        );
    }

    $built_parameters = get_all_constructor_parameters(
        $classname,
        $constructor_parameters,
        $processedValues
    );

    /**  @psalm-suppress MixedArgumentTypeCoercion */
    $object = $reflection_class->newInstanceArgs($built_parameters);

    /** @var T $object */
    return $object;
}

/**
 *
 *
 * @param DataStorage $dataStorage
 * @param GetType $typeExtractor
 * @return ValidationResult
 */
function createArrayOfTypeFromInputStorage(
    DataStorage $dataStorage,
    GetType $typeExtractor
): ValidationResult {

    // Setup variables to hold data over loop.
    $items = [];
    /** @var \DataType\ValidationProblem[] $allValidationProblems */
    $allValidationProblems = [];
    $processedValues = new ProcessedValues();
    $index = 0;

    $itemData = $dataStorage->getCurrentValue();

    if (is_array($itemData) !== true) {
        return ValidationResult::errorResult($dataStorage, Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1);
    }

    foreach ($itemData as $key => $_value) {
        $dataStorageForItem = $dataStorage->moveKey($key);

        $result = $typeExtractor->process(
            $processedValues,
            $dataStorageForItem
        );

        if ($result->anyErrorsFound() === true) {
            $allValidationProblems = array_merge(
                $allValidationProblems,
                $result->getValidationProblems()
            );
        }
        else {
            $items[$index] = $result->getValue();
        }

        $index += 1;
    }

    if (count($allValidationProblems) !== 0) {
        return ValidationResult::fromValidationProblems($allValidationProblems);
    }

    return ValidationResult::valueResult($items);
}



/**
 * @param string $className Class name implementing DataType (validated at runtime).
 * @return \DataType\InputType[]
 * @throws DataTypeDefinitionException
 * @throws MissingClassExceptionData
 * @throws DataTypeNotImplementedException
 */
function getInputTypeListForClass(string $className): array
{
    if (class_exists($className) !== true) {
        throw MissingClassExceptionData::fromClassname($className);
    }

    $implementsInterface = is_subclass_of(
        $className,
        DataType::class,
        $allow_string = true
    );

    if ($implementsInterface !== true) {
        throw DataTypeNotImplementedException::fromClassname($className);
    }

    // Type is okay, get data and validate
    $inputTypes = call_user_func([$className, 'getInputTypes']);


    // Validate all entries are InputTypes
    $index = 0;
    foreach ($inputTypes as $inputType) {
        if (!$inputType instanceof InputType) {
            throw DataTypeDefinitionException::foundNonPropertyDefinition($index, $className);
        }

        $index += 1;
    }

    // All okay, array contains only InputType items.
    /** @var \DataType\InputType[] $inputTypes */
    return $inputTypes;
}


/**
 * @param class-string<object> $classname
 * @param \ReflectionParameter[] $constructor_parameters
 * @param ProcessedValues $processedValues
 * @return mixed[]
 * @throws \ReflectionException
 * @throws NoConstructorExceptionData
 */
function get_all_constructor_parameters(
    string $classname,
    array $constructor_parameters,
    ProcessedValues $processedValues
) {
    $built_parameters = [];
    foreach ($constructor_parameters as $constructor_param) {
        $name = $constructor_param->getName();
        [$value, $available] = $processedValues->getValueForTargetProperty($name);
        if ($available !== true) {
            throw MissingConstructorParameterNameExceptionData::missingParam(
                $classname,
                $name
            );
        }
        $built_parameters[] = $value;
    }

    return $built_parameters;
}

/**
 * @param \DataType\HasInputType $hasDataType
 * @param mixed $inputValue
 * @return mixed
 * @throws Exception\DataTypeException
 * @throws ValidationException
 *
 * Validates and creates a single value according to the HasInputType
 * rules.
 *
 * The name part of HasInputType is needed, to be able to generate
 * appropriate error messages.
 */
function createSingleValue(HasInputType $hasDataType, mixed $inputValue)
{
    $inputType = $hasDataType->getInputType();

    $dataStorage = ArrayDataStorage::fromArray([
        $inputType->getName() => $inputValue
    ]);

    $processedValues = new ProcessedValues();

    $validationProblems = processInputTypesFromStorage(
        [$inputType],
        $processedValues,
        $dataStorage
    );

    if (count($validationProblems) !== 0) {
        throw new ValidationException(
            "Validation problems",
            $validationProblems
        );
    }

    $value = $processedValues->getValue($inputType->getName());

    return $value;
}


/**
 * @param \DataType\HasInputType $propertyInputType
 * @param mixed $inputValue
 * @return array{0:mixed, 1:\DataType\ValidationProblem[]}
 * @throws Exception\DataTypeException
 * @throws ValidationException
 *
 * Validates and creates a single value according to the HasInputType
 * rules.
 *
 * The name part of HasInputType is needed, to be able to generate
 * appropriate error messages.
 */
function createSingleValueOrError(HasInputType $propertyInputType, mixed $inputValue)
{
    $inputType = $propertyInputType->getInputType();

    $dataStorage = ArrayDataStorage::fromArray([
        $inputType->getName() => $inputValue
    ]);

    $processedValues = new ProcessedValues();

    $validationProblems = processInputTypesFromStorage(
        [$inputType],
        $processedValues,
        $dataStorage
    );

    if (count($validationProblems) !== 0) {
        return [null, $validationProblems];
    }

    $value = $processedValues->getValue($inputType->getName());

    return [$value, []];
}

/**
 * @param array<mixed> $array
 * @param mixed $value
 * @return bool
 */
function array_value_exists(array $array, $value): bool
{
    return in_array($value, $array, true);
}


/**
 * Separates an order parameter such as "+name", into the 'name' and
 * 'ordering' parts.
 * @param string $part
 * @return array{string, string}
 */
function normalise_order_parameter(string $part)
{
    if (str_starts_with($part, "+") === true) {
        return [substr($part, 1), Ordering::ASC];
    }

    if (str_starts_with($part, "-") === true) {
        return [substr($part, 1), Ordering::DESC];
    }

    return [$part, Ordering::ASC];
}


/**
 * @param mixed $value
 * @param DataStorage $dataStorage
 * @param ProcessRule ...$processRules
 * @return array{0:\DataType\ValidationProblem[], 1:?mixed}
 */
function processProcessingRules(
    $value,
    DataStorage $dataStorage,
    ProcessedValues $processedValues,
    ProcessRule ...$processRules
) {
    $validation_problems = [];

    foreach ($processRules as $processRule) {
        $validationResult = $processRule->process($value, $processedValues, $dataStorage);
        $validation_problems = array_merge(
            $validation_problems,
            $validationResult->getValidationProblems()
        );

        $value = $validationResult->getValue();
        if ($validationResult->isFinalResult() === true) {
            break;
        }
    }

    return [$validation_problems, $value];
}


/**
 * @param \DataType\InputType $dataType
 * @param ProcessedValues $processedValues
 * @param DataStorage $dataStorage
 * @return ValidationProblem[]
 */
function processInputTypeWithDataStorage(
    InputType       $dataType,
    ProcessedValues $processedValues,
    DataStorage     $dataStorage
) {
    // TODO - why are we moving here, rather than having it be part
    // of storage->foo(the params.)
    $dataStorageForItem = $dataStorage->moveKey($dataType->getName());
    $extractRule = $dataType->getExtractRule();
    $validationResult = $extractRule->process(
        $processedValues,
        $dataStorageForItem
    );

    if ($validationResult->anyErrorsFound()) {
        return $validationResult->getValidationProblems();
    }

    $value = $validationResult->getValue();

    // Process has already ended.
    if ($validationResult->isFinalResult() === true) {
        // TODO - modify here
        $processedValues->setValue($dataType, $value);
        return [];
    }

    // Extract rule wasn't a final result, so process
    [$validationProblems, $value] = processProcessingRules(
        $value,
        $dataStorageForItem,
        $processedValues,
        ...$dataType->getProcessRules()
    );

    // There were no validation problems, so store the value
    // so other parameter validators can reference it and it can
    // be used later.
    if (count($validationProblems) === 0) {
        $processedValues->setValue($dataType, $value);
    }

    return $validationProblems;
}

/**
 * @param HasInputType $param
 * @param ProcessedValues $processedValues
 * @param DataStorage $dataStorage
 * @return ValidationProblem[]
 */
function processSingleInputType(
    HasInputType    $param,
    ProcessedValues $processedValues,
    DataStorage     $dataStorage
): array {

    $inputType = $param->getInputType();
    return processInputTypeWithDataStorage(
        $inputType,
        $processedValues,
        $dataStorage
    );
}


/**
 * @param \DataType\InputType[] $dataTypeList
 * @param ProcessedValues $processedValues
 * @param DataStorage $dataStorage
 * @return \DataType\ValidationProblem[]
 */
function processInputTypesFromStorage(
    array           $dataTypeList,
    ProcessedValues $processedValues,
    DataStorage     $dataStorage
) {
    $validationProblems = [];

    foreach ($dataTypeList as $dataType) {
        $newValidationProblems = processInputTypeWithDataStorage(
            $dataType,
            $processedValues,
            $dataStorage
        );

        if (count($newValidationProblems) !== 0) {
            $validationProblems = array_merge($validationProblems, $newValidationProblems);
        }
    }

    return $validationProblems;
}

/**
 * Converts a string into the raw bytes, and displays
 * @param string $string
 * @return string
 */
function getRawCharacters(string $string): string
{
    $resultInHex = bin2hex($string);
    $resultSeparated = implode(', ', str_split($resultInHex, 2)); //byte safe

    return $resultSeparated;
}

/**
 * Get the list of default supported DateTime formats
 * @return string[]
 */
function getDefaultSupportedTimeFormats(): array
{
    return [
        \DateTime::ATOM,
        \DateTime::COOKIE,
        \DateTime::ISO8601,
        \DateTime::RFC822,
        \DateTime::RFC850,
        \DateTime::RFC1036,
        \DateTime::RFC1123,
        \DateTime::RFC2822,
        \DateTime::RFC3339,
        \DateTime::RFC3339_EXTENDED,
        \DateTime::RFC7231,
        \DateTime::RSS,
        \DateTime::W3C,
    ];
}

/**
 * @param string[] $allowedFormats
 * @return string[]
 * @throws InvalidDatetimeFormatExceptionData
 * @psalm-suppress DocblockTypeContradiction
 * @psalm-suppress RedundantConditionGivenDocblockType
 */
function checkAllowedFormatsAreStrings(array $allowedFormats): array
{
    $position = 0;
    foreach ($allowedFormats as $allowedFormat) {
        if (is_string($allowedFormat) !== true) {
            throw InvalidDatetimeFormatExceptionData::stringRequired($position, $allowedFormat);
        }
        $position += 1;
    }

    return $allowedFormats;
}


/**
 * @template T of object
 * @param class-string<object> $class
 * @param class-string<T> $attributeClassname
 * @return \ReflectionClass<T>
 */
function getReflectionClassOfAttribute(
    string $class,
    string $attributeClassname,
    \ReflectionProperty $property
): \ReflectionClass {
    if (class_exists($attributeClassname, true) !== true) {
        throw AnnotationClassDoesNotExistExceptionData::create(
            $class,
            $property->getName(),
            $attributeClassname
        );
    }

    return new \ReflectionClass($attributeClassname);
}

/**
 * @param class-string<object> $class
 * @return InputType[]
 * @throws \ReflectionException
 */
function getInputTypesFromAnnotations(string $class): array
{
    $rc = new \ReflectionClass($class);
    $inputTypes = [];

    foreach ($rc->getProperties() as $property) {
        $attributes = $property->getAttributes();
        $current_property_has_inputtype = false;
        foreach ($attributes as $attribute) {
            $attributeName = $attribute->getName();
            /** @var class-string<object> $attributeName */
            /** @psalm-suppress ArgumentTypeCoercion */
            $rc_of_attribute = getReflectionClassOfAttribute(
                $class,
                $attributeName,
                $property
            );
            $is_a_param = $rc_of_attribute->implementsInterface(HasInputType::class);

            if ($is_a_param !== true) {
                continue;
            }

            if ($current_property_has_inputtype === true) {
                throw PropertyHasMultipleInputTypeAnnotationsException::create(
                    $class,
                    $property->getName()
                );
            }

            $current_property_has_inputtype = true;
            $typeProperty = $attribute->newInstance();

            /** @var HasInputType $typeProperty */
            $inputType = $typeProperty->getInputType();
            $inputType->setTargetParameterName($property->getName());

            $inputTypes[] = $inputType;
        }
    }

    return $inputTypes;
}


/**
 * @param \DataType\ExtractRule\ExtractRule $extract_rule
 * @param DataStorage $dataStorage
 * @param ProcessRule[] $subsequentRules
 * @return ValidationResult
 * @throws \DataType\Exception\LogicExceptionData
 */
function createArrayOfScalarsFromDataStorage(
    DataStorage $dataStorage,
    ExtractRule $extract_rule,
    array       $subsequentRules
) {

    // Check its set
    if ($dataStorage->isValueAvailable() !== true) {
        return ValidationResult::errorResult($dataStorage, Messages::ERROR_MESSAGE_NOT_SET);
    }

    $itemData = $dataStorage->getCurrentValue();

    // Check its an array
    if (is_array($itemData) !== true) {
        return ValidationResult::errorResult($dataStorage, Messages::ERROR_MESSAGE_NOT_ARRAY);
    }

    // Setup stuff
    /** @var \DataType\ValidationProblem[] $validationProblems */
    $validationProblems = [];
    $index = 0;
    $new_processed_values = new ProcessedValues();

    // TODO - why do we look over this data like this? rather than
    // passing something to $datastorage->foo(...params);
    // Also, why don't we check the keys are ints?
    foreach ($itemData as $_itemDatum) {
        $dataType = new InputType((string)$index, $extract_rule, ...$subsequentRules);
        $new_validationProblems = processInputTypeWithDataStorage(
            $dataType,
            $new_processed_values,
            $dataStorage
        );

        $validationProblems = array_merge($validationProblems, $new_validationProblems);

        $index += 1;
    }

    if (count($validationProblems) !== 0) {
        return ValidationResult::fromValidationProblems($validationProblems);
    }

    return ValidationResult::valueResult($new_processed_values->getAllValues());
}



/**
 * Decode JSON with actual error detection
 *
 * @param string|null $json
 * @return mixed
 * @throws JsonDecodeException
 *
 * null is allowed, as a type, so that a meaningful error can be thrown.
 *
 */
function json_decode_safe(?string $json)
{
    if ($json === null) {
        throw new JsonDecodeException("Error decoding JSON: cannot decode null.");
    }

    $data = json_decode($json, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        if ($data === null) {
            throw new JsonDecodeException("Error decoding JSON: null returned.");
        }
        return $data;
    }

    $parser = new \Seld\JsonLint\JsonParser();
    $parsingException = $parser->lint($json);

    if ($parsingException !== null) {
        throw new JsonDecodeException(
            $parsingException->getMessage(),
            $parsingException->getCode(),
            $parsingException
        );
    }

    // This should never be reached.
    // @codeCoverageIgnoreStart
    throw new JsonDecodeException("Error decoding JSON: " . json_last_error_msg());
    // @codeCoverageIgnoreEnd
}


/**
 * @param mixed $data
 * @param int $options
 * @return string
 * @throws JsonEncodeException
 */
function json_encode_safe($data, $options = 0): string
{
    $result = json_encode($data, $options);

    if ($result === false) {
        throw new JsonEncodeException("Failed to encode data as json: " . json_last_error_msg());
    }

    return $result;
}
