<?php

declare(strict_types=1);


namespace TypeSpecTest;

use Attribute;
use TypeSpec\HasDataType;
use TypeSpec\DataType;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\ImagickIsRgbColor;

// This InputTypeSpec is repeatable, so that it can be used more
// than once solely for testing purposes. It is not expected for
// people to use Attribute::IS_REPEATABLE normally.
#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class ImagickColorHasDataType implements HasDataType
{
    public function __construct(
        private string $default,
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetStringOrDefault($this->default),
            new ImagickIsRgbColor()
        );
    }
}
