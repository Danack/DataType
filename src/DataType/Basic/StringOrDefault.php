<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetStringOrDefault;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\SkipIfNull;

/**
 * String input with a default when the parameter is missing.
 */
#[\Attribute]
class StringOrDefault implements HasInputType
{
    public function __construct(
        private string $name,
        private string|null $default,
        private int $maxLength = 1_000_000,
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrDefault($this->default),
            new SkipIfNull(),
            new MaxLength($this->maxLength),
        );
    }
}
