<?php

declare(strict_types = 1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationResult;
use function DataType\createArrayOfScalarsFromDataStorage;

/**
 * Extracts an array of float values, and then applies a list of ProcessPropertyRules
 * to them.
 */
class GetArrayOfFloat implements ExtractRule
{
    /** @var ProcessRule[] */
    private array $subsequentRules;

    public function __construct(ProcessRule ...$rules)
    {
        $this->subsequentRules = $rules;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {

        $extract_rule = new GetFloat();

        return createArrayOfScalarsFromDataStorage(
            $dataStorage,
            $extract_rule,
            $this->subsequentRules
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
