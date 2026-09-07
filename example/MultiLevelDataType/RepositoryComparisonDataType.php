<?php

declare(strict_types=1);

namespace MultiLevelDataType;

use DataType\Create\CreateFromArray;
use DataType\Create\CreateFromJson;
use DataType\GetInputTypesFromAttributes;
use DataType\DataType;
use DataType\Basic\BasicString;
use DataType\ExtractRule\EasierGetType;




class RepositoryComparisonDataType implements DataType
{
    use CreateFromJson;
    use CreateFromArray;

    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('repository')]
        public readonly string $repository,

        #[GetTypeParam('base', BranchReference::class)]
        public readonly BranchReference $first,
        #[GetTypeParam('compare', BranchReference::class)]
        public readonly BranchReference $second,
    ) {
    }
}