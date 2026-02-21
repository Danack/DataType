<?php


namespace DataTypeTest\InputType;

use DataType\ExtractRule\GetArrayOfType;
use DataType\HasInputType;
use DataType\InputType;
use DataTypeTest\DTOTypes\BasicDTO;

#[\Attribute]
class MultipleBasicDTO implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetArrayOfType(BasicDTO::class),
        );
    }
}
