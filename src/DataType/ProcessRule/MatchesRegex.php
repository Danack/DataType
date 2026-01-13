<?php

declare(strict_types=1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Exception\LogicExceptionData;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Validates that a string matches a regular expression pattern.
 */
class MatchesRegex implements ProcessRule
{
    use CheckString;

    private string $pattern;
    private ?string $flags;

    /**
     * @param string $pattern The regex pattern to match against
     * @param string|null $flags Optional flags for preg_match (e.g., 'i' for case-insensitive)
     */
    public function __construct(string $pattern, ?string $flags = null)
    {
        $this->pattern = $pattern;
        $this->flags = $flags;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkString($value);

        // Build the full pattern with delimiters and flags
        $fullPattern = $this->pattern;
        
        // If pattern doesn't have delimiters, add them
        $hasDelimiters = preg_match('/^\/.+\/[imsxADSUXJu]*$/', $this->pattern) === 1;
        if (!$hasDelimiters) {
            $fullPattern = '/' . $this->pattern . '/';
        }
        
        // Append flags if provided
        if ($this->flags !== null) {
            // Remove existing flags and add new ones
            $replaced = preg_replace('/\/[imsxADSUXJu]*$/', '/' . $this->flags, $fullPattern);
            // @codeCoverageIgnoreStart
            if ($replaced === null) {
                throw new LogicExceptionData("preg_replace failed for pattern: " . $fullPattern);
            }
            // @codeCoverageIgnoreEnd
            $fullPattern = $replaced;
        }

        $matches = preg_match($fullPattern, $value);

        // @codeCoverageIgnoreStart
        if ($matches === false) {
            throw new LogicExceptionData("preg_match failed for pattern: " . $fullPattern);
        }
        // @codeCoverageIgnoreEnd

        if ($matches !== 1) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::ERROR_PATTERN_MISMATCH
            );
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Extract pattern without delimiters and flags for OpenAPI
        $patternForOpenApi = $this->pattern;
        $matches = [];
        if (preg_match('/^\/(.+)\/[imsxADSUXJu]*$/', $this->pattern, $matches) === 1) {
            $patternForOpenApi = $matches[1];
        }
        $paramDescription->setPattern($patternForOpenApi);
    }
}
