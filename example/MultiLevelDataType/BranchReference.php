<?php

declare(strict_types=1);

namespace MultiLevelDataType;

use DataType\Basic\BasicSha;
use DataType\Basic\BasicString;
use DataType\Create\CreateFromArray;
use DataType\GetInputTypesFromAttributes;
use DataType\DataType;


class BranchReference implements DataType
{
    use CreateFromArray;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('branch')]
        public readonly string $branch,
        #[BasicSha('sha')]
        public readonly string $sha,
    ) {
    }
}

