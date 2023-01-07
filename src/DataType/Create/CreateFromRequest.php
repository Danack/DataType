<?php

declare(strict_types=1);

namespace DataType\Create;

use Psr\Http\Message\ServerRequestInterface;
use DataType\DataStorage\ArrayDataStorage;
use VarMap\Psr7VarMap;
use function DataType\create;
use function DataType\getInputTypeListForClass;

/**
 * Creates a DataType from a PSR7 ServerRequest or throws a ValidationException if there is a
 * a problem validating the data.
 */
trait CreateFromRequest
{
    /**
     * @param ServerRequestInterface $request
     * @return self
     * @throws \DataType\Exception\ValidationException
     */
    public static function createFromRequest(ServerRequestInterface $request)
    {
        $variableMap = new Psr7VarMap($request);
        $inputTypeList = getInputTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        $object = create(static::class, $inputTypeList, $dataStorage);
        /** @var $object self */
        return $object;
    }
}
