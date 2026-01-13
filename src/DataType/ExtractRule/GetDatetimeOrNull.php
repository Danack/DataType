<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Extracts a DateTime value or null.
 *
 * This pattern is probably 'not best practice' for an API, and just not including the key/value
 * is possibly better to indicate a lack of an optional value.
 */
class GetDatetimeOrNull implements ExtractRule
{
    private GetDatetime $getDatetime;

    /**
     * @param string[]|null $allowedFormats
     */
    public function __construct(?array $allowedFormats = null)
    {
        $this->getDatetime = new GetDatetime($allowedFormats);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $value = $dataStorage->getCurrentValue();

        if (is_null($value) === true) {
            return ValidationResult::valueResult($value);
        }

        return $this->getDatetime->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setRequired(true);
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
        $paramDescription->setNullAllowed(true);
    }
}
