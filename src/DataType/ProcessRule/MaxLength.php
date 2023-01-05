<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Exception\InvalidRulesExceptionData;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that the length of a string is at most a certain number of characters.
 */
class MaxLength implements ProcessRule
{
    use CheckString;

    private int $maxLength;

    /**
     * MaxLengthValidator constructor.
     * @param int $maxLength
     */
    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @throws InvalidRulesExceptionData
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        if (mb_strlen($value) > $this->maxLength) {
            $message = sprintf(
                Messages::STRING_TOO_LONG,
                $this->maxLength
            );
            return ValidationResult::errorResult($inputStorage, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMaxLength($this->maxLength);
    }
}
