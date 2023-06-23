<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that input is a valid date of the format 'Y-m-d'.
 */
class ValidUrl implements ProcessRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkString($value);

        $regex = '/^https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/';

        $matches = preg_match(
            $regex,
            $value
        );

        if ($matches === 1) {
            return ValidationResult::valueResult($value);
        };

        return ValidationResult::errorResult(
            $inputStorage,
            Messages::ERROR_INVALID_URL
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATE);
    }
}
