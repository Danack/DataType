<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use DataType\Exception\ValidationException;
use DataType\ExtractRule\GetType;
use function DataType\createArrayOfTypeFromInputStorage;

/**
 * Creates an array of DataType from a plain array or throws a ValidationException if there is a
 * a problem validating the data.
 *
 * Using this avoid needing to create a 'collection type' to hold other DataTypes.
 */
trait CreateArrayOfTypeFromArray
{
    /**
     * @param array<mixed> $data
     * @return static[]
     * @throws \DataType\Exception\ValidationException
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
            throw new ValidationException(
                "Validation problems",
                $validationResult->getValidationProblems()
            );
        }

        $objects = $validationResult->getValue();

        /** @var static[] $objects */
        return $objects;
    }
}
