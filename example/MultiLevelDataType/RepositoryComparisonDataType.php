<?php

declare(strict_types=1);

namespace MultiLevelDataType;

use DataType\Create\CreateFromArray;
use DataType\Create\CreateFromJson;
use DataType\GetInputTypesFromAttributes;
use DataType\DataType;
use DataType\Basic\BasicString;
use DataType\ExtractRule\EasierGetType;
use DataType\InputType\GetDataType;

/**
 * An example 'DataType' class that represents a type of data. You should
 * probably name the imlementing classes how you think about types of data
 * Some examples:
 * - the body of an API call, I call those 'Params'
 * - the input parameters for a command line programme, also 'Params'.
 * - A representation of an external API call, again Params.
 *
 *
 */
class RepositoryComparisonDataType implements DataType
{
    use CreateFromJson;
    use CreateFromArray;

    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('repository')]
        public readonly string $repository,

        #[GetDataType('base', BranchReference::class)]
        public readonly BranchReference $base,
        #[GetDataType('comparison', BranchReference::class)]
        public readonly BranchReference $comparison,
    ) {
    }

    public function info(): string
    {
        $info = sprintf("Respository is: %s\n", $this->repository);
        $info .= sprintf("Base branch: %s, SHA: %s\n", $this->base->branch, $this->base->sha);
        $info .= sprintf("Comparison branch: %s, SHA: %s\n", $this->comparison->branch, $this->comparison->sha);

        return $info;
    }
}