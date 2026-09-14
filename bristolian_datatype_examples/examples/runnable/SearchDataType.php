<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Runnable;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Basic\BasicString;
use DataType\Create\CreateFromRequest;
use DataType\Create\CreateOrErrorFromRequest;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

class SearchDataType implements DataType
{
    use CreateFromRequest;
    use CreateOrErrorFromRequest;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('search')]
        public readonly string $search,
        #[BasicIntegerOrDefault('limit', 10)]
        public readonly int $limit,
    ) {
    }
}
