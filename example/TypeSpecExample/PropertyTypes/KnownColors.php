<?php


namespace TypeSpecExample\PropertyTypes;

use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\Enum;
use TypeSpec\DataType;
use TypeSpec\HasDataType;

#[\Attribute]
class KnownColors implements HasDataType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetStringOrDefault('blue'),
            new Enum(['red', 'green', 'blue'])
        );
    }
}
