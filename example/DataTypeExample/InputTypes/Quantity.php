<?php

namespace DataTypeExample\InputTypes;

use DataType\ExtractRule\GetInt;
use DataType\InputType;
use DataType\HasInputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinIntValue;

#[\Attribute]
class Quantity implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetInt(),
            new MinIntValue(1),
            new MaxIntValue(20),
        );
    }
}
