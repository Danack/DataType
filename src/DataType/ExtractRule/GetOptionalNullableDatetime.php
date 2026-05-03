<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Tri-state optional datetime: absent, explicit null, or present string (then parse like GetDatetime).
 */
class GetOptionalNullableDatetime implements ExtractRule
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
            return ValidationResult::finalValueResult(Absent::instance());
        }

        if ($dataStorage->getCurrentValue() === null) {
            return ValidationResult::finalValueResult(PresentNull::instance());
        }

        return $this->getDatetime->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setRequired(false);
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
        $paramDescription->setNullAllowed(true);
    }
}
