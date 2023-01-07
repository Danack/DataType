<?php

namespace DataType;

// Hi there.
//
// This file contains public functions that are meant to be used by people
// using the DataType library.

use DataType\DataStorage\ArrayDataStorage;
use DataType\DataStorage\ComplexDataStorage;
use DataType\DataStorage\DataStorage;
use DataType\Exception\MissingClassExceptionData;
use DataType\Exception\DataTypeDefinitionException;
use DataType\Exception\DataTypeNotImplementedException;
use DataType\Exception\ValidationException;
use DataType\ExtractRule\GetType;
use DataType\OpenApi\OpenApiV300ParamDescription;

/**
 * @template T
 * @param class-string<T> $classname
 * @param \DataType\InputType[] $inputTypeList
 * @param DataStorage $dataStorage
 * @return T of object
 * @throws ValidationException
 * @throws \ReflectionException
 */
function create(
    $classname,
    $inputTypeList,
    DataStorage $dataStorage
) {
    $processedValues = new ProcessedValues();

    $validationProblems = processInputTypesFromStorage(
        $inputTypeList,
        $processedValues,
        $dataStorage
    );

    if (count($validationProblems) !== 0) {
        throw new ValidationException("Validation problems", $validationProblems);
    }
    $object = createObjectFromProcessedValues($classname, $processedValues);

    /** @var T $object */
    return $object;
}

/**
 * @template T
 * @param class-string<T> $classname
 * @param \DataType\InputType[] $inputTypes
 * @param DataStorage $dataStorage
 * @return array{0:?object, 1:\DataType\ValidationProblem[]}
 * @throws Exception\DataTypeException
 * @throws ValidationException
 *
 * The rules are passed separately to the classname so that we can
 * support rules coming both from static info and from factory objects.
 */
function createOrError($classname, $inputTypes, DataStorage $dataStorage)
{
    $processedValues = new ProcessedValues();

    $validationProblems = processInputTypesFromStorage(
        $inputTypes,
        $processedValues,
        $dataStorage
    );

    if (count($validationProblems) !== 0) {
        return [null, $validationProblems];
    }

    $object = createObjectFromProcessedValues($classname, $processedValues);

    return [$object, []];
}


/**
 * @param object $dto
 * @return array{0:?object, 1:\DataType\ValidationProblem[]}
 * @throws Exception\DataTypeException
 * @throws DataTypeDefinitionException
 * @throws MissingClassExceptionData
 * @throws DataTypeNotImplementedException
 * @throws ValidationException
 */
function validate(object $dto)
{
    $class = get_class($dto);

    $dataTypeListForClass = getInputTypeListForClass($class);

    $dataStorage = ComplexDataStorage::fromData($dto);

    [$object, $validationProblems] = createOrError(
        $class,
        $dataTypeListForClass,
        $dataStorage
    );

    return [$object, $validationProblems];
}




/**
 * @template T
 * @param string $type
 * @psalm-param class-string<T> $type
 * @param array $data
 * @return T[]
 * @throws ValidationException
 */
function createArrayOfType(string $type, array $data): array
{
    $dataStorage = ArrayDataStorage::fromArray($data);
    $getType = GetType::fromClass($type);
    $validationResult = createArrayOfTypeFromInputStorage($dataStorage, $getType);

    if ($validationResult->anyErrorsFound()) {
        throw new ValidationException(
            "Validation problems",
            $validationResult->getValidationProblems()
        );
    }

    return $validationResult->getValue();
}


/**
 * @template T
 * @param string $type
 * @psalm-param class-string<T> $type
 * @param array $data
 * @return array{0:null, 1:\DataType\ValidationProblem[]}|array{0:T[], 1:null}
 */
function createArrayOfTypeOrError(string $type, array $data): array
{
    $dataStorage = ArrayDataStorage::fromArray($data);
    $getType = GetType::fromClass($type);
    $validationResult = createArrayOfTypeFromInputStorage($dataStorage, $getType);

    if ($validationResult->anyErrorsFound()) {
        return [null, $validationResult->getValidationProblems()];
    }

    $finalValue = $validationResult->getValue();
    /** @var T[] $finalValue */

    return [$finalValue, null];
}

function generateOpenApiV300DescriptionForDataType(string $classname)
{
    $implementsInterface = is_subclass_of(
        $classname,
        DataType::class,
        $allow_string = true
    );

    if ($implementsInterface !== true) {
        throw DataTypeNotImplementedException::fromClassname($classname);
    }

    // Type is okay, get data
    $inputTypes = call_user_func([$classname, 'getInputTypes']);

    return OpenApiV300ParamDescription::createFromInputTypes($inputTypes);
}