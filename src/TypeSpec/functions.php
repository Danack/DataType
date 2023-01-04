<?php

namespace TypeSpec;

// Hi there.
//
// This file contains public functions that are meant to be used by people
// using the TypeSpec library.

use TypeSpec\DataStorage\ArrayDataStorage;
use TypeSpec\DataStorage\ComplexDataStorage;
use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Exception\MissingClassException;
use TypeSpec\Exception\TypeDefinitionException;
use TypeSpec\Exception\TypeNotInputParameterListException;
use TypeSpec\Exception\ValidationException;
use TypeSpec\ExtractRule\GetType;

/**
 * @template T
 * @param class-string<T> $classname
 * @param \TypeSpec\DataType[] $inputTypeList
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

    $validationProblems = processDataTypeList(
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
 * @param \TypeSpec\DataType[] $inputTypeSpecList
 * @param DataStorage $dataStorage
 * @return array{0:?object, 1:\TypeSpec\ValidationProblem[]}
 * @throws Exception\TypeSpecException
 * @throws ValidationException
 *
 * The rules are passed separately to the classname so that we can
 * support rules coming both from static info and from factory objects.
 */
function createOrError($classname, $inputTypeSpecList, DataStorage $dataStorage)
{
    $processedValues = new ProcessedValues();

    $validationProblems = processDataTypeList(
        $inputTypeSpecList,
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
 * @return array{0:?object, 1:\TypeSpec\ValidationProblem[]}
 * @throws Exception\TypeSpecException
 * @throws TypeDefinitionException
 * @throws MissingClassException
 * @throws TypeNotInputParameterListException
 * @throws ValidationException
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
 * @return array{0:null, 1:\TypeSpec\ValidationProblem[]}|array{0:T[], 1:null}
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
