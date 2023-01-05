<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use VarMap\ArrayVarMap;
use function JsonSafe\json_decode_safe;
use function DataType\create;
use function DataType\getDataTypeListForClass;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromJson
{
    /**
     * @param string $json
     * @return self
     * @throws \DataType\Exception\ValidationExceptionData
     */
    public static function createFromJson($json)
    {
        $rules = getDataTypeListForClass(self::class);
        $data = json_decode_safe($json);
        $dataStorage = ArrayDataStorage::fromArray($data);

        $object = create(
            static::class,
            $rules,
            $dataStorage
        );

        /** @var $object self */
        return $object;
    }
}
