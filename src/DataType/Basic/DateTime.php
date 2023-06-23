<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetDatetime;
use DataType\ExtractRule\GetInt;
use DataType\InputType;
use DataType\HasInputType;
use DataType\ExtractRule\GetString;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\MaxLength;

#[\Attribute]
class DateTime implements HasInputType
{
    /**
     * @param string $name
     * @param array|null $supportedFormats e.g. ["Y-m-d H:i:s"] see `getDefaultSupportedTimeFormats` for
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
