<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetOptionalDatetime;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional datetime input. When the parameter is missing, the property receives null.
 * Accepts common ISO/RFC formats; optional second constructor argument restricts to specific formats.
 */
#[\Attribute]
class OptionalDateTime implements HasInputType
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
            new GetOptionalDatetime($this->allowedFormats),
            new SkipIfNull(),
        );
    }
}
