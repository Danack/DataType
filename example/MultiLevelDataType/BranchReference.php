<?php

declare(strict_types=1);

namespace MultiLevelDataType;

use DataType\Basic\BasicInteger;
use DataType\Basic\BasicString;
use DataType\GetInputTypesFromAttributes;
use DataType\DataType;

class BranchReference implements DataType
{
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('name')]
        public readonly string $quantity,
        #[BasicSHA('sha')]
        public readonly int $sha,
    ) {
    }
}

