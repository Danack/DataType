<?php


namespace TypeSpecTest\PropertyTypes;

use TypeSpec\ExtractRule\GetArrayOfType;
use TypeSpec\DataType;
use TypeSpec\HasDataType;
use TypeSpecTest\DTOTypes\BasicDTO;

#[\Attribute]
class MultipleBasicDTO implements HasDataType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetArrayOfType(BasicDTO::class),
        );
    }
}
