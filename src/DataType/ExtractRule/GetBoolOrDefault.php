<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\CastToBool;
use DataType\ValidationResult;

/**
 * Extracts a boolean value, or a default value if the paramter is not set.
 *
 * bool(true) - true
 * bool(false) - false
 * string(true) - true
 * string(false) - false
 * any other input - error
 *
 */
class GetBoolOrDefault implements ExtractRule
{
    private ?bool $default;

    /**
     * setOrDefaultValidator constructor.
     * @param bool $default The default value to use.
     */
    public function __construct(?bool $default)
    {
        $this->default = $default;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $intRule = new CastToBool();

        return $intRule->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
