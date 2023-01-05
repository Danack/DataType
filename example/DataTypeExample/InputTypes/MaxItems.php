<?php

namespace DataTypeExample\InputTypes;

use DataType\InputType;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\HasInputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinIntValue;

#[\Attribute]
class MaxItems implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetIntOrDefault(20),
            new MinIntValue(1),
            new MaxIntValue(200),
        );
    }
}
