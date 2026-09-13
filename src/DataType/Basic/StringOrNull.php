<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetStringOrNull;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\SkipIfNull;

/**
 * Required parameter that may be null. When the value is present it must be a string; when null, the property receives null.
 */
#[\Attribute]
class StringOrNull implements HasInputType
{
    public function __construct(
        private string $name,
        private int $maxLength = 1_000_000,
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrNull(),
            new SkipIfNull(),
            new MaxLength($this->maxLength),
        );
    }
}
