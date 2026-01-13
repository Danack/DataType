<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\CastToFloat;
use DataType\ValidationResult;

/**
 * Extracts a floating point value or a default value if the parameter is not available.
 */
class GetFloatOrDefault implements ExtractRule
{
    private ?float $default;

    /**
     * @param float $default
     */
    public function __construct(?float  $default)
    {
        $this->default = $default;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {

        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $floatInput = new CastToFloat();

        return $floatInput->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
