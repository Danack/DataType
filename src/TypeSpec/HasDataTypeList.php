<?php

declare(strict_types = 1);


namespace TypeSpec;

/**
 *
 */
interface HasDataTypeList
{
    /**
     * @return \TypeSpec\DataType[]
     */
    public static function getDataTypeList(): array;
}
