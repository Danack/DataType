<?php


namespace TypeSpecTest\PropertyTypes;

use TypeSpec\ExtractRule\GetArrayOfType;
use TypeSpec\DataType;
use TypeSpec\HasDataType;
//use ParamsTest\DTOTypes\BasicDTO;
use TypeSpecTest\PropertyTypes\Quantity;

#[\Attribute]
class MultipleBasicArray implements HasDataType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetArrayOfType(Quantity::class),
        );
    }
}
