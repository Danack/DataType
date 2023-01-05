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
class MinLength implements ProcessRule
{
    use CheckString;

    private int $minLength;

    public function __construct(int $minLength)
    {
        $this->minLength = $minLength;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        if (mb_strlen($value) < $this->minLength) {
            $message = sprintf(
                Messages::STRING_TOO_SHORT,
                $this->minLength
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
        $paramDescription->setMinLength($this->minLength);
    }
}
