<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use function DataType\json_decode_safe;
use function DataType\createOrError;
use function DataType\getInputTypeListForClass;

/**
 * Creates a DataType from JSON.
 *
 * Returns two values, the DataType created or null and an array of ValidationProblems if there were any.
 */
trait CreateOrErrorFromJson
{
    /**
     * @param array $data
     * TODO - ValidationErrors is incorrect.
     * @return array{0:?object, 1:\DataType\ValidationProblem[]}
     * @throws \DataType\Exception\ValidationException
     */
    public static function createOrErrorFromJson($json)
    {
        $data = json_decode_safe($json);
        $inputTypeList = getInputTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        return createOrError(static::class, $inputTypeList, $dataStorage);
    }
}
