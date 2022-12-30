<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Exception\InvalidRulesException;
use TypeSpec\Exception\LogicException;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Class CheckOnlyAllowedCharacters
 *
 * Checks that an input string contains only allowed characters.
 * Flags used for preg_match are xu
 *
 */
class CheckOnlyAllowedCharacters implements ProcessPropertyRule
{
    use CheckString;

    private string $patternValidCharacters;

    public function __construct(string $patternValidCharacters)
    {
        $this->patternValidCharacters = $patternValidCharacters;
    }

    /**
     * @throws LogicException
     * @throws InvalidRulesException
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
            throw new LogicException("preg_match failed");
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
