<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use VarMap\VarMap;
use function DataType\createOrError;
use function DataType\getInputTypeListForClass;

/**
 * Creates a DataType from a VarMap.
 *
 * Returns two values, the DataType created or null and an array of ValidationProblems if there were any.
 */
trait CreateOrErrorFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return array{0:?object, 1:\DataType\ValidationProblem[]}
     * @throws \DataType\Exception\ValidationException
     */
    public static function createOrErrorFromVarMap(VarMap $variableMap)
    {
        $inputTypeList = getInputTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        return createOrError(static::class, $inputTypeList, $dataStorage);
    }
}
