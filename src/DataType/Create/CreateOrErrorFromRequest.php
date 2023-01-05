<?php

declare(strict_types=1);

namespace DataType\Create;

use Psr\Http\Message\ServerRequestInterface;
use DataType\DataStorage\ArrayDataStorage;
use DataType\Exception;
use VarMap\Psr7VarMap;
use function DataType\createOrError;
use function DataType\getDataTypeListForClass;

trait CreateOrErrorFromRequest
{
    /**
     * @param ServerRequestInterface $request
     * @return array{0:object|null, 1:ValidationErrors|null}
     * @throws Exception\DataTypeException
     * @throws Exception\ValidationExceptionData
     */
    public static function createOrErrorFromRequest(ServerRequestInterface $request)
    {
        $variableMap = new Psr7VarMap($request);
        $rules = getDataTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        return createOrError(static::class, $rules, $dataStorage);
    }
}
