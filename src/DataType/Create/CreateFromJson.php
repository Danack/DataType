<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use VarMap\ArrayVarMap;
use function JsonSafe\json_decode_safe;
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
     * @return self
     * @throws \DataType\Exception\ValidationException
     */
    public static function createFromJson($json)
    {
        $inputTypeList = getInputTypeListForClass(self::class);
        $data = json_decode_safe($json);
        $dataStorage = ArrayDataStorage::fromArray($data);

        $object = create(
            static::class,
            $inputTypeList,
            $dataStorage
        );

        /** @var $object self */
        return $object;
    }
}
