<?php


namespace TypeSpecTest\PropertyTypes;

use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\Enum;
use TypeSpec\DataType;
use TypeSpec\HasDataType;

#[\Attribute]
class KnownColors implements HasDataType
{
    const KNOWN_COLORS = ['red', 'green', 'blue'];

    public function __construct(
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetStringOrDefault('blue'),
            new Enum(self::KNOWN_COLORS)
        );
    }
}
