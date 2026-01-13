<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Extracts a DateTime value or a default value if the parameter is not available.
 */
class GetDatetimeOrDefault implements ExtractRule
{
    private ?\DateTimeInterface $default;

    private GetDatetime $getDatetime;

    /**
     * @param \DateTimeInterface|null $default
     * @param string[]|null $allowedFormats
     */
    public function __construct(?\DateTimeInterface $default, ?array $allowedFormats = null)
    {
        $this->default = $default;
        $this->getDatetime = new GetDatetime($allowedFormats);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        return $this->getDatetime->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setRequired(false);
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
        // Note: default is a DateTimeInterface which can't be directly set as OpenAPI default
    }
}
