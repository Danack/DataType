<?php

declare(strict_types=1);


namespace DataTypeTest;

use Attribute;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\ProcessRule\ImagickIsRgbColor;

// This InputTypeSpec is repeatable, so that it can be used more
// than once solely for testing purposes. It is not expected for
// people to use Attribute::IS_REPEATABLE normally.
#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class ImagickColorHasInputType implements HasInputType
{
    public function __construct(
        private string $default,
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrDefault($this->default),
            new ImagickIsRgbColor()
        );
    }
}
