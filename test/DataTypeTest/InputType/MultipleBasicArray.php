<?php


namespace DataTypeTest\InputType;

use DataType\ExtractRule\GetArrayOfType;
use DataType\InputType;
use DataType\HasInputType;
//use ParamsTest\DTOTypes\BasicDTO;
use DataTypeTest\PropertyTypes\Quantity;

#[\Attribute]
class MultipleBasicArray implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetArrayOfType(Quantity::class),
        );
    }
}
