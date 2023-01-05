<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use function JsonSafe\json_decode_safe;
use function DataType\createOrError;
use function DataType\getDataTypeListForClass;

trait CreateOrErrorFromJson
{
    /**
     * @param array $data
     * TODO - ValidationErrors is incorrect.
     * @return array{0:self|null, 1:\DataType\ValidationErrors|null}
     * @throws \DataType\Exception\ValidationExceptionData
     */
    public static function createOrErrorFromJson($json)
    {
        $data = json_decode_safe($json);

        $rules = getDataTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        return createOrError(static::class, $rules, $dataStorage);
    }
}
