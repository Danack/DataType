<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Runnable;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Basic\OptionalBasicString;
use DataType\Basic\OptionalBool;
use DataType\Create\CreateFromArray;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

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
