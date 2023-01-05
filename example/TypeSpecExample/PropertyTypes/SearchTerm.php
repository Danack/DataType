<?php

namespace TypeSpecExample\PropertyTypes;

use TypeSpec\ExtractRule\GetString;
use TypeSpec\DataType;
use TypeSpec\HasDataType;
use TypeSpec\ProcessRule\MaxLength;
use TypeSpec\ProcessRule\MinLength;

#[\Attribute]
class SearchTerm implements HasDataType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetString(),
            new MinLength(3),
            new MaxLength(200),
        );
    }
}
