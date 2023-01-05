<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Exception\InvalidRulesException;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Checks that the length of a string is at most a certain number of characters.
 */
class MaxLength implements ProcessPropertyRule
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
     * @throws InvalidRulesException
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
