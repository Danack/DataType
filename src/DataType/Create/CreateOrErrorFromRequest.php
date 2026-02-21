<?php

declare(strict_types=1);

namespace DataType\Create;

use DataType\DataStorage\ArrayDataStorage;
use DataType\Exception;
use Psr\Http\Message\ServerRequestInterface;
use VarMap\Psr7VarMap;
use function DataType\createOrError;
use function DataType\getInputTypeListForClass;

/**
 * Creates a DataType from a PSR7 ServerRequest.
 *
 * Returns two values, the DataType created or null and an array of ValidationProblems if there were any.
 */
trait CreateOrErrorFromRequest
{
    /**
     * @param ServerRequestInterface $request
     * @return array{0:?object, 1:\DataType\ValidationProblem[]}
     * @throws Exception\DataTypeException
     * @throws Exception\ValidationException
     */
    public static function createOrErrorFromRequest(ServerRequestInterface $request)
    {
        $variableMap = new Psr7VarMap($request);
        $inputTypeList = getInputTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        return createOrError(static::class, $inputTypeList, $dataStorage);
    }
}
