<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetOptionalString;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\PhpEnum;
use DataType\ProcessRule\SkipIfNull;

/**
 * Optional backed-enum input. When the parameter is missing, the property receives null. Value must match a case of the given enum.
 */
#[\Attribute]
class BasicPhpEnumTypeOrNull implements HasInputType
{
    /**
     * @param string $name
     * @param class-string<\BackedEnum> $enum_type
     */
    public function __construct(
        private string $name,
        private string $enum_type
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetOptionalString(),
            new SkipIfNull(),
            new PhpEnum($this->enum_type)
        );
    }
}
