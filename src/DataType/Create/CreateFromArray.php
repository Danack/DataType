<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use VarMap\VarMap;
use function DataType\create;
use function DataType\getInputTypeListForClass;

/**
 * Creates a DataType from a plain array or throws a ValidationException if there is a
 * a problem validating the data.
 */
trait CreateFromArray
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \DataType\Exception\ValidationException
     */
    public static function createFromArray($data): static
    {
        $inputTypeList = getInputTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($data);

        $object = create(
            static::class,
            $inputTypeList,
            $dataStorage
        );

        /** @var $object static */
        return $object;
    }
}
