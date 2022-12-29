<?php

declare(strict_types=1);


namespace TypeSpecTest;

use TypeSpec\HasDataType;
use TypeSpec\DataType;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\IsRgbColor;

class ColorHasDataType implements HasDataType
{
    public function __construct(
        private string $name,
        private string $default
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetStringOrDefault($this->default),
            new IsRgbColor()
        );
    }
}
