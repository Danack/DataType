<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that the length of a string is at least a certain number of characters.
 */
class ExactLength implements ProcessRule
{
    use CheckString;

    private int $length;

    public function __construct(int $length)
    {
        $this->length = $length;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        $string_length = mb_strlen($value);
        if ($string_length !== $this->length) {
            $message = sprintf(
                Messages::STRING_EXACT_LENGTH,
                $string_length,
                $this->length
            );

            return ValidationResult::errorResult(
                $inputStorage,
                $message
            );
        }
        return ValidationResult::valueResult($value);
    }


    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinLength($this->length);
        $paramDescription->setMaxLength($this->length);
    }
}
