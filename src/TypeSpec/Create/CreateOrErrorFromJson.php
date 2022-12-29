<?php

declare(strict_types=1);

namespace TypeSpec\Create;

use TypeSpec\DataStorage\ArrayDataStorage;
use function JsonSafe\json_decode_safe;
use function TypeSpec\createOrError;
use function TypeSpec\getDataTypeListForClass;

trait CreateOrErrorFromJson
{
    /**
     * @param array $data
     * TODO - ValidationErrors is incorrect.
     * @return array{0:self|null, 1:\TypeSpec\ValidationErrors|null}
     * @throws \TypeSpec\Exception\ValidationException
     */
    public static function createOrErrorFromJson($json)
    {
        $data = json_decode_safe($json);

        $rules = getDataTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        return createOrError(static::class, $rules, $dataStorage);
    }
}
