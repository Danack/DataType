<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that input is a valid datetime of the format \DateTime::RFC3339
 *
 */
class ValidDatetime implements ProcessRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value  = $this->checkString($value);

        // TODO - Change this to \DateTime::RFC3339_EXTENDED when
        // someone complains.
        $dateTime = \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $value);
        if ($dateTime instanceof \DateTimeInterface) {
            return ValidationResult::valueResult($dateTime);
        }

        return ValidationResult::errorResult($inputStorage, Messages::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
    }
}
