<?php


namespace DataTypeTest\InputType;

use DataType\ExtractRule\GetStringOrDefault;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\Enum;

#[\Attribute]
class KnownColors implements HasInputType
{
    const KNOWN_COLORS = ['red', 'green', 'blue'];

    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrDefault('blue'),
            new Enum(self::KNOWN_COLORS)
        );
    }
}
