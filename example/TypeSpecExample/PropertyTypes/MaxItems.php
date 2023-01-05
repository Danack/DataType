<?php

namespace TypeSpecExample\PropertyTypes;

use TypeSpec\DataType;
use TypeSpec\ExtractRule\GetIntOrDefault;
use TypeSpec\HasDataType;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MinIntValue;

#[\Attribute]
class MaxItems implements HasDataType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetIntOrDefault(20),
            new MinIntValue(1),
            new MaxIntValue(200),
        );
    }
}
