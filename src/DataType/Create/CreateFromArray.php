<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use VarMap\VarMap;
use function DataType\create;
use function DataType\getDataTypeListForClass;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \DataType\Exception\ValidationExceptionData
     */
    public static function createFromArray($data)
    {
        $rules = getDataTypeListForClass(self::class);

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
