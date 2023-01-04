<?php

namespace TypeSpec;

// Hi there.
//
// This file contains functions that are used internally by the TypeSpec
// library. If you think you need to use one, feel free to do so, but also
// let us know why, and maybe we'll move it to the public functions.php file.

use TypeSpec\DataStorage\ArrayDataStorage;
use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Exception\AnnotationClassDoesNotExistException;
use TypeSpec\Exception\IncorrectNumberOfParametersException;
use TypeSpec\Exception\InvalidDatetimeFormatException;
use TypeSpec\ExtractRule\ExtractPropertyRule;
use TypeSpec\Exception\LogicException;
use TypeSpec\Exception\MissingClassException;
use TypeSpec\Exception\MissingConstructorParameterNameException;
use TypeSpec\Exception\NoConstructorException;
use TypeSpec\Exception\PropertyHasMultipleInputTypeSpecAnnotationsException;
use TypeSpec\Exception\TypeDefinitionException;
use TypeSpec\Exception\TypeNotInputParameterListException;
use TypeSpec\Exception\ValidationException;
use TypeSpec\ExtractRule\GetType;
use TypeSpec\ProcessRule\ProcessPropertyRule;
use TypeSpec\Value\Ordering;

/**
 * @template T
 * @param class-string<T> $classname
 * @param ProcessedValues $processedValues
 * @return T of object
 * @throws \ReflectionException
 * @throws NoConstructorException
 */
function createObjectFromProcessedValues(string $classname, ProcessedValues $processedValues)
{
    $reflection_class = new \ReflectionClass($classname);

    $r_constructor = $reflection_class->getConstructor();

    // No constructor, can't create the object with parameters.
    // Yes this is an arbitrary choice, but it seems sensible.
    if ($r_constructor === null) {
        throw NoConstructorException::noConstructor($classname);
    }

    if ($r_constructor->isPublic() !== true) {
        throw NoConstructorException::notPublicConstructor($classname);
    }

    $constructor_parameters = $r_constructor->getParameters();
    if (count($constructor_parameters) !== $processedValues->getCount()) {
        throw IncorrectNumberOfParametersException::wrongNumber(
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
    /** @var \TypeSpec\ValidationProblem[] $allValidationProblems */
    $allValidationProblems = [];
    $processedValues = new ProcessedValues();
    $index = 0;

    $itemData = $dataStorage->getCurrentValue();

    if (is_array($itemData) !== true) {
        return ValidationResult::errorResult($dataStorage, Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1);
    }

    foreach ($itemData as $key => $value) {
        $dataStorageForItem = $dataStorage->moveKey($key);

        $result = $typeExtractor->process(
            $processedValues,
            $dataStorageForItem
        );

        if ($result->anyErrorsFound() === true) {
            $allValidationProblems = [...$allValidationProblems, ...$result->getValidationProblems()];
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
 * @param string $className
 * @return \TypeSpec\DataType[]
 * @throws TypeDefinitionException
 * @throws MissingClassException
 * @throws TypeNotInputParameterListException
 */
function getDataTypeListForClass(string $className): array
{
    if (class_exists($className) !== true) {
        throw MissingClassException::fromClassname($className);
    }

    // TODO - fold into single function
    $inputParameterList = getDataTypeListFromAnnotations($className);

    if (count($inputParameterList) === 0) {
        $implementsInterface = is_subclass_of(
            $className,
            HasDataTypeList::class,
            $allow_string = true
        );

        if ($implementsInterface !== true) {
            throw TypeNotInputParameterListException::fromClassname($className);
        }

        // Type is okay, get data and validate
        $inputParameterList = call_user_func([$className, 'getDataTypeList']);
    }
    // TODO - end fold into single function

    // Validate all entries are InputParameters
    $index = 0;
    foreach ($inputParameterList as $inputParameter) {
        if (!$inputParameter instanceof DataType) {
            throw TypeDefinitionException::foundNonPropertyDefinition($index, $className);
        }

        $index += 1;
    }

    // All okay, array contains only Param items.
    /** @var \TypeSpec\DataType[] $inputParameterList */
    return $inputParameterList;
}


/**
 * @template T
 * @param class-string<T> $classname
 * @param \ReflectionParameter[] $constructor_parameters,
 * @param ProcessedValues $processedValues
 * @return mixed[]
 * @throws \ReflectionException
 * @throws NoConstructorException
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
            throw MissingConstructorParameterNameException::missingParam(
                $classname,
                $name
            );
        }
        $built_parameters[] = $value;
    }

    return $built_parameters;
}

/**
 * @param \TypeSpec\HasDataType $hasDataType
 * @param mixed $inputValue
 * @return mixed
 * @throws Exception\TypeSpecException
 * @throws ValidationException
 *
 * Validates and creates a single value according to the PropertyInputTypeSpec
 * rules.
 *
 * The name part of PropertyInputTypeSpec is needed, to be able to generate
 * appropriate error messages.
 */
function createSingleValue(HasDataType $hasDataType, mixed $inputValue)
{
    $inputTypeSpec = $hasDataType->getDataType();

    $dataStorage = ArrayDataStorage::fromArray([
        $inputTypeSpec->getName() => $inputValue
    ]);

    $processedValues = new ProcessedValues();
    $inputTypeSpecList = [$inputTypeSpec];

    $validationProblems = processDataTypeList(
        $inputTypeSpecList,
        $processedValues,
        $dataStorage
    );

    if (count($validationProblems) !== 0) {
        throw new ValidationException(
            "Validation problems",
            $validationProblems
        );
    }

    $value = $processedValues->getValue($inputTypeSpec->getName());

    return $value;
}


/**
 * @param \TypeSpec\HasDataType $propertyInputTypeSpec
 * @param mixed $inputValue
 * @return array{0:mixed, 1:\TypeSpec\ValidationProblem[]}
 * @throws Exception\TypeSpecException
 * @throws ValidationException
 *
 * Validates and creates a single value according to the PropertyInputTypeSpec
 * rules.
 *
 * The name part of PropertyInputTypeSpec is needed, to be able to generate
 * appropriate error messages.
 */
function createSingleValueOrError(HasDataType $propertyInputTypeSpec, mixed $inputValue)
{
    $inputTypeSpec = $propertyInputTypeSpec->getDataType();

    $dataStorage = ArrayDataStorage::fromArray([
        $inputTypeSpec->getName() => $inputValue
    ]);

    $processedValues = new ProcessedValues();
    $inputTypeSpecList = [$inputTypeSpec];

    $validationProblems = processDataTypeList(
        $inputTypeSpecList,
        $processedValues,
        $dataStorage
    );

    if (count($validationProblems) !== 0) {
        return [null, $validationProblems];
    }

    $value = $processedValues->getValue($inputTypeSpec->getName());

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
 * @param string|int $value  The value of the variable
 * @return null|string returns an error string, when there is an error
 */
function check_only_digits(string|int $value)
{
    if (is_int($value) === true) {
        return null;
    }

    $count = preg_match("/[^0-9]+/", $value, $matches, PREG_OFFSET_CAPTURE);

    if ($count === false) {
        // @codeCoverageIgnoreStart
        // This seems impossible to test.
        throw new LogicException("preg_match failed");
        // @codeCoverageIgnoreEnd
    }

    if ($count !== 0) {
        /**  @psalm-suppress MixedArrayAccess */
        $badCharPosition = $matches[0][1];

        /** @psalm-suppress MixedArgument */
        $message = sprintf(
            Messages::INT_REQUIRED_FOUND_NON_DIGITS,
            $badCharPosition
        );
        return $message;
    }

    return null;
}


/**
 * Separates an order parameter such as "+name", into the 'name' and
 * 'ordering' parts.
 * @param string $part
 * @return array{string, string}
 */
function normalise_order_parameter(string $part)
{
    if (substr($part, 0, 1) === "+") {
        return [substr($part, 1), Ordering::ASC];
    }

    if (substr($part, 0, 1) === "-") {
        return [substr($part, 1), Ordering::DESC];
    }

    return [$part, Ordering::ASC];
}


/**
 * @param mixed $value
 * @param DataStorage $dataStorage
 * @param ProcessPropertyRule ...$processRules
 * @return array{0:\TypeSpec\ValidationProblem[], 1:?mixed}
 */
function processProcessingRules(
    $value,
    DataStorage $dataStorage,
    ProcessedValues $processedValues,
    ProcessPropertyRule ...$processRules
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
 * @param \TypeSpec\DataType $dataType
 * @param ProcessedValues $processedValues
 * @param DataStorage $dataStorage
 * @return ValidationProblem[]
 */
function processDataTypeWithDataStorage(
    DataType        $dataType,
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
 * @param HasDataType $param
 * @param ProcessedValues $paramValues
 * @param DataStorage $dataStorage
 * @return ValidationProblem[]
 */
function processSingleInputType(
    HasDataType     $param,
    ProcessedValues $paramValues,
    DataStorage     $dataStorage
): array {

    $inputParameter = $param->getDataType();
    return processDataTypeWithDataStorage(
        $inputParameter,
        $paramValues,
        $dataStorage
    );
}


/**
 * @param \TypeSpec\DataType[] $dataTypeList
 * @param ProcessedValues $processedValues
 * @param DataStorage $dataStorage
 * @return \TypeSpec\ValidationProblem[]
 */
function processDataTypeList(
    array           $dataTypeList,
    ProcessedValues $processedValues,
    DataStorage     $dataStorage
) {
    $validationProblems = [];

    foreach ($dataTypeList as $dataType) {
        $newValidationProblems = processDataTypeWithDataStorage(
            $dataType,
            $processedValues,
            $dataStorage
        );

        if (count($newValidationProblems) !== 0) {
            $validationProblems = [...$validationProblems, ...$newValidationProblems];
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
 * @throws InvalidDatetimeFormatException
 * @psalm-suppress DocblockTypeContradiction
 * @psalm-suppress RedundantConditionGivenDocblockType
 */
function checkAllowedFormatsAreStrings(array $allowedFormats): array
{
    $position = 0;
    foreach ($allowedFormats as $allowedFormat) {
        if (is_string($allowedFormat) !== true) {
            throw InvalidDatetimeFormatException::stringRequired($position, $allowedFormat);
        }
        $position += 1;
    }

    return $allowedFormats;
}


function getReflectionClassOfAttribute(
    string $class,
    string $attributeClassname,
    \ReflectionProperty $property
): \ReflectionClass {
    if (class_exists($attributeClassname, true) !== true) {
        throw AnnotationClassDoesNotExistException::create(
            $class,
            $property->getName(),
            $attributeClassname
        );
    }

    return new \ReflectionClass($attributeClassname);
}

/**
 * @template T
 * @param string|object $class
 * @psalm-param class-string<T> $class
 * @return DataType[]
 * @throws \ReflectionException
 */
function getDataTypeListFromAnnotations(string $class): array
{
    $rc = new \ReflectionClass($class);
    $inputParameters = [];

    foreach ($rc->getProperties() as $property) {
        $attributes = $property->getAttributes();
        $current_property_has_typespec = false;
        foreach ($attributes as $attribute) {
            $rc_of_attribute = getReflectionClassOfAttribute(
                $class,
                $attribute->getName(),
                $property
            );
            $is_a_param = $rc_of_attribute->implementsInterface(HasDataType::class);

            if ($is_a_param !== true) {
                continue;
            }

            if ($current_property_has_typespec == true) {
                throw PropertyHasMultipleInputTypeSpecAnnotationsException::create(
                    $class,
                    $property->getName()
                );
            }

            $current_property_has_typespec = true;
            $typeProperty = $attribute->newInstance();

            /** @var HasDataType $typeProperty */
            $inputParameter = $typeProperty->getDataType();
            $inputParameter->setTargetParameterName($property->getName());

            $inputParameters[] = $inputParameter;
        }
    }

    return $inputParameters;
}


/**
 * @param \TypeSpec\ExtractRule\ExtractPropertyRule $extract_rule
 * @param DataStorage $dataStorage
 * @param ProcessPropertyRule[] $subsequentRules
 * @return ValidationResult
 * @throws \TypeSpec\Exception\LogicException
 */
function createArrayOfScalarsFromDataStorage(
    DataStorage $dataStorage,
    ExtractPropertyRule $extract_rule,
    array $subsequentRules
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
    /** @var \TypeSpec\ValidationProblem[] $validationProblems */
    $validationProblems = [];
    $index = 0;
    $new_processed_values = new ProcessedValues();

    // TODO - why do we look over this data like this? rather than
    // passing something to $datastorage->foo(...params);
    // Also, why don't we check the keys are ints?
    foreach ($itemData as $_itemDatum) {
        $dataType = new DataType((string)$index, $extract_rule, ...$subsequentRules);
        $new_validationProblems = processDataTypeWithDataStorage(
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
