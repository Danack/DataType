<?php


namespace DataTypeExample\InputTypes;

use DataType\ExtractRule\GetStringOrDefault;
use DataType\ProcessRule\Enum;
use DataType\InputType;
use DataType\HasInputType;

#[\Attribute]
class KnownColors implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrDefault('blue'),
            new Enum(['red', 'green', 'blue'])
        );
    }
}
