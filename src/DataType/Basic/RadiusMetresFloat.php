<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetFloat;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\PositiveFloat;

/**
 * Required float input for a radius in metres (0 or greater).
 */
#[\Attribute]
class RadiusMetresFloat implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetFloat(),
            new PositiveFloat(),
        );
    }
}
