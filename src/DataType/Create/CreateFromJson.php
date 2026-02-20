<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use DataType\Exception\ValidationException;
use DataType\ValidationProblem;
use VarMap\ArrayVarMap;
use function DataType\json_decode_safe;
use function DataType\create;
use function DataType\getInputTypeListForClass;

/**
 * Creates a DataType from JSON or throws a ValidationException if there is a
 * a problem validating the data.
 */
trait CreateFromJson
{
    /**
     * @param string $json
     * @return static
     * @throws \DataType\Exception\ValidationException
     */
    public static function createFromJson(string $json): static
    {
        $inputTypeList = getInputTypeListForClass(self::class);
        $data = json_decode_safe($json);
        if (!\is_array($data)) {
            $rootStorage = ArrayDataStorage::fromArray([]);
            throw new ValidationException(
                'Invalid JSON structure',
                [new ValidationProblem(
                    $rootStorage,
                    'JSON root must be an object (associative array), got ' . \get_debug_type($data)
                )]
            );
        }
        $dataStorage = ArrayDataStorage::fromArray($data);

        $object = create(
            static::class,
            $inputTypeList,
            $dataStorage
        );

        return $object;
    }
}
