<?php


namespace DataTypeTest\InputType;

use DataType\ExtractRule\GetStringOrDefault;
use DataType\ProcessRule\Enum;
use DataType\InputType;
use DataType\HasInputType;

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
