<?php

declare(strict_types=1);

namespace TypeSpec\Create;

use TypeSpec\DataStorage\ArrayDataStorage;
use VarMap\VarMap;
use function TypeSpec\createOrError;
use function TypeSpec\getDataTypeListForClass;

trait CreateOrErrorFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return array{0:?object, 1:\TypeSpec\ValidationProblem[]}
     * @throws \TypeSpec\Exception\ValidationException
     */
    public static function createOrErrorFromVarMap(VarMap $variableMap)
    {
        $rules = getDataTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        return createOrError(static::class, $rules, $dataStorage);
    }
}
