<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use VarMap\VarMap;
use function DataType\createOrError;
use function DataType\getDataTypeListForClass;

trait CreateOrErrorFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return array{0:?object, 1:\DataType\ValidationProblem[]}
     * @throws \DataType\Exception\ValidationExceptionData
     */
    public static function createOrErrorFromVarMap(VarMap $variableMap)
    {
        $rules = getDataTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        return createOrError(static::class, $rules, $dataStorage);
    }
}
