<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataType\ProcessRule\CastToFloat;
use DataType\ValidationResult;

/**
 * Tri-state optional float: absent, explicit null, or present value (then cast/process rules apply).
 */
class GetOptionalNullableFloat implements ExtractRule
{
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

        $castToFloat = new CastToFloat();

        return $castToFloat->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setRequired(false);
        $paramDescription->setNullAllowed(true);
    }
}
