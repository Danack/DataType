<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use function DataType\createOrError;
use function DataType\getDataTypeListForClass;

trait CreateOrErrorFromArray
{
    /**
     * @param array $data
     * TODO - ValidationErrors is incorrect.
     * @return array{0:self|null, 1:\DataType\ValidationErrors|null}
     * @throws \DataType\Exception\ValidationExceptionData
     */
    public static function createOrErrorFromArray(array $data)
    {
        $rules = getDataTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        return createOrError(static::class, $rules, $dataStorage);
    }
}
