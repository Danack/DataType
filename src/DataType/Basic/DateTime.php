<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetDatetime;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Required datetime input. Accepts common ISO/RFC formats; optional constructor argument restricts to specific formats.
 */
#[\Attribute]
class DateTime implements HasInputType
{
    /**
     * @param string $name
     * @param string[]|null $supportedFormats e.g. ["Y-m-d H:i:s"] see `getDefaultSupportedTimeFormats` for
     * default list of formats.
     */
    public function __construct(
        private string $name,
        private array|null $supportedFormats = null
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetDatetime($this->supportedFormats),
        );
    }
}
