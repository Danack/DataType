<?php

declare(strict_types=1);

namespace DataType\Create;

use Psr\Http\Message\ServerRequestInterface;
use DataType\DataStorage\ArrayDataStorage;
use VarMap\Psr7VarMap;
use function DataType\create;
use function DataType\getDataTypeListForClass;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromRequest
{
    /**
     * @param ServerRequestInterface $request
     * @return self
     * @throws \DataType\Exception\ValidationExceptionData
     */
    public static function createFromRequest(ServerRequestInterface $request)
    {
        $variableMap = new Psr7VarMap($request);
        $rules = getDataTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        $object = create(static::class, $rules, $dataStorage);
        /** @var $object self */
        return $object;
    }
}
