<?php

declare(strict_types=1);

/**
 * Standard usage: custom HasInputType (follows the opening search example).
 * Doc extract: Example_standard_custom_property_type
 */

namespace BristolianDatatypeExamples\Standard;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Create\CreateFromRequest;
use DataType\DataType;
use DataType\ExtractRule\GetString;
use DataType\GetInputTypesFromAttributes;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\Trim;

// Example_standard_custom_property_type start
#[\Attribute]
class SearchTerm implements HasInputType
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
            new Trim(),
            new MinLength(3),
            new MaxLength(200),
        );
    }
}

class SearchDataTypeWithRules implements DataType
{
    use CreateFromRequest;
    use GetInputTypesFromAttributes;

    public function __construct(
        // Read the value from the "search" field and process it using the rules in SearchTerm
        #[SearchTerm('search')]
        public readonly string $search,
        // Read the value from the "limit" field and process it using the rules in BasicIntegerOrDefault (default 10)
        #[BasicIntegerOrDefault('limit', 10)]
        public readonly int $limit,
    ) {
    }
}
// Example_standard_custom_property_type end
