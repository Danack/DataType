<?php

namespace DataType\Basic;

use DataType\InputType;
use DataType\HasInputType;
use DataType\ExtractRule\GetString;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\ValidUrl;

/**
 * Required string input validated as a URL (with scheme). Min length 12, max 2048.
 */
#[\Attribute]
class Url implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new MinLength(12),
            new MaxLength(2048),
            new ValidUrl(true),
        );
    }
}
