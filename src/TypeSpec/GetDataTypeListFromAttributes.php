<?php

declare(strict_types = 1);

namespace TypeSpec;

trait GetDataTypeListFromAttributes
{
    /**
     * @return \TypeSpec\DataType[]
     * @throws \ReflectionException
     */
    public static function getDataTypeList(): array
    {
        return getDataTypeListFromAnnotations(get_called_class());
    }
}
