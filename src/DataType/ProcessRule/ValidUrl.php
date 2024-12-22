<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that input is a valid url with scheme
 */
class ValidUrl implements ProcessRule
{
    use CheckString;

    public function __construct(private bool $scheme_required)
    {
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        $value = $this->checkString($value);

        $regex = '/^(https?:\\/\\/)?(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/';

        if ($this->scheme_required === true) {
            $regex = '/^https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/';
        }



        /* TODO - this regular expression check may be more thorough

// https://stackoverflow.com/a/44029246/778719
$regularExpression  = "((https?|ftp)\:\/\/)?"; // SCHEME Check
$regularExpression .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass Check
$regularExpression .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP Check
$regularExpression .= "(\:[0-9]{2,5})?"; // Port Check
$regularExpression .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path Check
$regularExpression .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query String Check
$regularExpression .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor Check
        */

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
