<?php

declare(strict_types = 1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\ProcessPropertyRule;
use TypeSpec\ValidationResult;
use function TypeSpec\createArrayOfScalarsFromDataStorage;

/**
 * Extracts an array of int values, and then applies a list of ProcessPropertyRules
 * to them.
 */
class GetArrayOfInt implements ExtractPropertyRule
{
    /** @var ProcessPropertyRule[] */
    private array $subsequentRules;

    public function __construct(ProcessPropertyRule ...$rules)
    {
        $this->subsequentRules = $rules;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {

        $extract_rule = new GetInt();

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
