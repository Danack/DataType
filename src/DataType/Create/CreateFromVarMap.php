<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use DataType\Exception\DataTypeDefinitionException;
use DataType\Exception\DataTypeNotImplementedException;
use DataType\Exception\MissingClassExceptionData;
use DataType\Exception\ValidationException;
use VarMap\VarMap;
use function DataType\create;
use function DataType\getInputTypeListForClass;

/**
 * Creates a DataType from a VarMap or throws a ValidationException if there is a
 * a problem validating the data.
 */
trait CreateFromVarMap
{
    /**
     * @param VarMap $variableMap
     * @return static
     * @throws DataTypeDefinitionException
     * @throws DataTypeNotImplementedException
     * @throws MissingClassExceptionData
     * @throws ValidationException
     */
    public static function createFromVarMap(VarMap $variableMap): static
    {
        $inputTypeList = getInputTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());
        $object = create(static::class, $inputTypeList, $dataStorage);
        /** @var $object static */
        return $object;
    }
}
