<?php

namespace TypeSpecTest\PropertyTypes;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\DataType;
use TypeSpec\HasDataType;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MinIntValue;

#[\Attribute]
class Quantity implements HasDataType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetInt(),
            new MinIntValue(1),
            new MaxIntValue(20),
        );
    }
}
