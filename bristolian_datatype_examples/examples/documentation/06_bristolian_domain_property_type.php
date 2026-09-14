<?php

declare(strict_types=1);

/**
 * Bristolian-style: domain property type (not a Basic* duplicate).
 * Doc extract: Example_bristolian_domain_property_type
 *
 * Modelled on Bristolian\Parameters\PropertyType\RoomNoteTitle.
 */

namespace BristolianDatatypeExamples\Bristolian;

use DataType\ExtractRule\GetString;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\RangeStringLength;
use DataType\ProcessRule\TrimOrNull;

// Example_bristolian_domain_property_type start
#[\Attribute]
class RoomNoteTitle implements HasInputType
{
    public const MINIMUM_LENGTH = 1;

    public const MAXIMUM_LENGTH = 1024;

    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new TrimOrNull(),
            new RangeStringLength(self::MINIMUM_LENGTH, self::MAXIMUM_LENGTH),
        );
    }
}

#[\Attribute]
class RoomNoteMarkdown implements HasInputType
{
    public const MINIMUM_LENGTH = 1;

    public const MAXIMUM_LENGTH = 65535;

    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new RangeStringLength(self::MINIMUM_LENGTH, self::MAXIMUM_LENGTH),
        );
    }
}
// Example_bristolian_domain_property_type end
