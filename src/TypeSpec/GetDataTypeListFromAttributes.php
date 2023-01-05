<?php

declare(strict_types = 1);

namespace TypeSpec;

trait GetDataTypeListFromAttributes
{
    // If PHP would allow traits to implement interfaces
    // this would implement \TypeSpec\HasDataTypeList

    /**
     * @return \TypeSpec\DataType[]
     * @throws \ReflectionException
     */
    public static function getDataTypeList(): array
    {
        return getDataTypeListFromAnnotations(get_called_class());
    }
}
