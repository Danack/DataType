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
use DataType\Exception\ValidationExceptionData;
use DataType\ExtractRule\GetType;

/**
 * @template T
 * @param class-string<T> $classname
 * @param \DataType\InputType[] $inputTypeList
 * @param DataStorage $dataStorage
 * @return T of object
 * @throws ValidationExceptionData
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
        throw new ValidationExceptionData("Validation problems", $validationProblems);
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
 * @throws ValidationExceptionData
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
 * @throws ValidationExceptionData
 */
function validate(object $dto)
{
    $class = get_class($dto);

    $dataTypeListForClass = getDataTypeListForClass($class);

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
 * @throws ValidationExceptionData
 */
function createArrayOfType(string $type, array $data): array
{
    $dataStorage = ArrayDataStorage::fromArray($data);
    $getType = GetType::fromClass($type);
    $validationResult = createArrayOfTypeFromInputStorage($dataStorage, $getType);

    if ($validationResult->anyErrorsFound()) {
        throw new ValidationExceptionData(
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
