<?php

namespace DataTypeExample\InputTypes;

use DataType\ExtractRule\GetString;
use DataType\InputType;
use DataType\HasInputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinLength;

#[\Attribute]
class SearchTerm implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new MinLength(3),
            new MaxLength(200),
        );
    }
}
