<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;
use function DataType\checkAllowedFormatsAreStrings;
use function DataType\getDefaultSupportedTimeFormats;

/**
 * Extracts a DateTime value.
 *
 * The list of formats allowed can be passed in. The default list of formats is:
 *
 * DateTime::ATOM,
 * DateTime::COOKIE,
 * DateTime::ISO8601,
 * DateTime::RFC822,
 * DateTime::RFC850,
 * DateTime::RFC1036,
 * DateTime::RFC1123,
 * DateTime::RFC2822,
 * DateTime::RFC3339,
 * DateTime::RFC3339_EXTENDED,
 * DateTime::RFC7231,
 * DateTime::RSS,
 * DateTime::W3C,
 *
 */
class GetDatetime implements ExtractRule
{
    /**
     * @var string[]
     */
    private array $allowedFormats;

    /**
     *
     * @param string[]|null $allowedFormats
     */
    public function __construct(?array $allowedFormats = null)
    {
        if ($allowedFormats === null) {
            $this->allowedFormats = getDefaultSupportedTimeFormats();
            return;
        }

        $this->allowedFormats = checkAllowedFormatsAreStrings($allowedFormats);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $value = $dataStorage->getCurrentValue();

        if (is_array($value) === true) {
            return ValidationResult::errorResult($dataStorage, Messages::ERROR_DATETIME_MUST_START_AS_STRING);
        }

        if (is_string($value) !== true) {
            return ValidationResult::errorResult(
                $dataStorage,
                Messages::ERROR_DATETIME_MUST_START_AS_STRING,
            );
        }

        foreach ($this->allowedFormats as $allowedFormat) {
            $dateTime = \DateTimeImmutable::createFromFormat($allowedFormat, $value);

            if ($dateTime instanceof \DateTimeInterface) {
                return ValidationResult::valueResult($dateTime);
            }
        }

        return ValidationResult::errorResult($dataStorage, Messages::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setRequired(true);
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
    }
}
