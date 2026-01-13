<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\ProcessRule;
use DataType\ValidationResult;
use function DataType\createArrayOfScalarsFromDataStorage;

/**
 * Extracts an array of DateTime values, and then applies a list of ProcessPropertyRules
 * to them.
 */
class GetArrayOfDatetime implements ExtractRule
{
    /** @var ProcessRule[] */
    private array $subsequentRules;

    private GetDatetime $getDatetime;

    /**
     * @param string[]|null $allowedFormats
     * @param ProcessRule ...$rules
     */
    public function __construct(?array $allowedFormats = null, ProcessRule ...$rules)
    {
        $this->subsequentRules = $rules;
        $this->getDatetime = new GetDatetime($allowedFormats);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {

        return createArrayOfScalarsFromDataStorage(
            $dataStorage,
            $this->getDatetime,
            $this->subsequentRules
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
