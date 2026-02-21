<?php

declare(strict_types=1);


namespace DataTypeTest;

use DataType\ExtractRule\GetStringOrDefault;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\IsRgbColor;

class ColorHasInputType implements HasInputType
{
    public function __construct(
        private string $name,
        private string $default
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrDefault($this->default),
            new IsRgbColor()
        );
    }
}
