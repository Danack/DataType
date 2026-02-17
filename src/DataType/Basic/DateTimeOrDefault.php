<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetDatetimeOrDefault;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Datetime input with a default when the parameter is missing.
 * Optional second constructor argument restricts to specific formats.
 */
#[\Attribute]
class DateTimeOrDefault implements HasInputType
{
    /**
     * @param string $name
     * @param \DateTimeInterface|null $default
     * @param string[]|null $allowedFormats
     */
    public function __construct(
        private string $name,
        private \DateTimeInterface|null $default,
        private ?array $allowedFormats = null
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetDatetimeOrDefault($this->default, $this->allowedFormats),
        );
    }
}
