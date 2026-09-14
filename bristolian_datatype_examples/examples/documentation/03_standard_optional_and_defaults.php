<?php

declare(strict_types=1);

/**
 * Standard usage: optional fields and defaults via DataType\Basic.
 * Doc extract: Example_standard_optional_and_defaults
 */

namespace BristolianDatatypeExamples\Standard;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Basic\OptionalBasicString;
use DataType\Basic\OptionalBool;
use DataType\Create\CreateFromArray;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

// Example_standard_optional_and_defaults start
class ListFilterParams implements DataType
{
    use CreateFromArray;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[OptionalBasicString('query')]
        public readonly string|null $query,
        #[BasicIntegerOrDefault('limit', 20)]
        public readonly int $limit,
        #[OptionalBool('include_archived', default: false)]
        public readonly bool $include_archived,
    ) {
    }
}

// Missing keys are fine:
$params = ListFilterParams::createFromArray([]);
// $params->query === null, $params->limit === 20, $params->include_archived === false
// Example_standard_optional_and_defaults end
