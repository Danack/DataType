<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Exception\InvalidRulesExceptionData;
use DataType\Exception\LogicExceptionData;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Class CheckOnlyAllowedCharacters
 *
 * Checks that an input string contains only allowed characters.
 * Flags used for preg_match are xu
 *
 * Example usage:
 * ```php
 * use DataType\InputType;
 * use DataType\ExtractRule\GetString;
 * use DataType\ProcessRule\CheckOnlyAllowedCharacters;
 *
 * // Allow only alphanumeric characters
 * $inputType = new InputType(
 *     'username',
 *     new GetString(),
 *     new CheckOnlyAllowedCharacters('a-zA-Z0-9')
 * );
 *
 * // Allow only letters
 * $inputType = new InputType(
 *     'name',
 *     new GetString(),
 *     new CheckOnlyAllowedCharacters('a-zA-Z')
 * );
 * ```
 *
 */
class CheckOnlyAllowedCharacters implements ProcessRule
{
    use CheckString;

    private string $patternValidCharacters;

    public function __construct(string $patternValidCharacters)
    {
        $this->patternValidCharacters = $patternValidCharacters;
    }

    /**
     * @throws LogicExceptionData
     * @throws InvalidRulesExceptionData
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        $patternInvalidCharacters = "/[^" . $this->patternValidCharacters . "]+/xu";
        $matches = [];
        $count = preg_match($patternInvalidCharacters, $value, $matches, PREG_OFFSET_CAPTURE);

        // @codeCoverageIgnoreStart
        if ($count === false) {
            throw new LogicExceptionData("preg_match failed");
        }
        // @codeCoverageIgnoreEnd

        if ($count !== 0) {
            $badCharPosition = $matches[0][1];
            $message = sprintf(
                Messages::STRING_FOUND_INVALID_CHAR,
                $badCharPosition,
                $this->patternValidCharacters
            );
            return ValidationResult::errorResult($inputStorage, $message);
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setPattern($this->patternValidCharacters);
    }
}
