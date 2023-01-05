<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use DataType\Exception\ValidationExceptionData;
use DataType\ExtractRule\GetType;
use VarMap\VarMap;
use function DataType\createArrayOfTypeFromInputStorage;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateArrayOfTypeFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self[]
     * @throws \DataType\Exception\ValidationExceptionData
     */
    public static function createArrayOfTypeFromArray(array $data)
    {
        $getType = GetType::fromClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        $validationResult = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        if ($validationResult->anyErrorsFound() === true) {
            throw new ValidationExceptionData(
                "Validation problems",
                $validationResult->getValidationProblems()
            );
        }

        $objects = $validationResult->getValue();

        /** @var self[] self */
        return $objects;
    }
}
