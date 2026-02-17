<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetDatetimeOrNull;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Required parameter that may be null. When the value is present it must be a valid datetime; when null, the property receives null.
 * Optional second constructor argument restricts to specific formats.
 */
#[\Attribute]
class DateTimeOrNull implements HasInputType
{
    /**
     * @param string $name
     * @param string[]|null $allowedFormats
     */
    public function __construct(
        private string $name,
        private ?array $allowedFormats = null
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetDatetimeOrNull($this->allowedFormats),
        );
    }
}
