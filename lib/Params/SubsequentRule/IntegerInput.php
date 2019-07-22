<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

/**
 * Checks a value is an integer that has a sane value
 */
class IntegerInput implements SubsequentRule
{
    const MAX_SANE_VALUE = 999999999999999;

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        // TODO - check is null
        if (is_int($value) !== true) {
            $value = (string)$value;
            if (strlen($value) === 0) {
                return ValidationResult::errorResult(
                    $name,
                    "Value is an empty string - must be an integer."
                );
            }

            // check string length is not zero length.
            $match = preg_match("/[^0-9]+/", $value);

            if ($match !== 0) {
                return ValidationResult::errorResult($name, "Value must contain only digits.");
            }
        }

        $maxSaneLength = strlen((string)(self::MAX_SANE_VALUE));

        if (strlen((string)$value) > $maxSaneLength) {
            $message = sprintf(
                "Value for %s too long, max %s digits",
                $name,
                $maxSaneLength
            );

            return ValidationResult::errorResult($name, $message);
        }

        return ValidationResult::valueResult(intval($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // todo - this seems like a not needed rule.
    }
}