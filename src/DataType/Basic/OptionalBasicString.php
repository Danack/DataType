<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetOptionalString;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\SkipIfNull;

#[\Attribute]
class OptionalBasicString implements HasInputType
{
    public function __construct(
        private string|null $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetOptionalString(),
            new SkipIfNull(),
        );
    }
}
