<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Exception\LogicExceptionData;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Checks that one parameter represents an later time than another parameter
 * by a set number of minutes.
 */
class LaterThanParam implements ProcessRule
{
    private string $paramToCompare;

    private int $minutesLater;

    /**
     * @param string $paramToCompare The name of the param this one should be the same as.
     * @param int $minutesLater how many minutes later this time needs to be
     */
    public function __construct(string $paramToCompare, int $minutesLater)
    {
        $this->paramToCompare = $paramToCompare;
        $this->minutesLater = $minutesLater;

        if ($minutesLater < 0) {
            throw new LogicExceptionData(Messages::MINUTES_MUST_BE_GREATER_THAN_ZERO);
        }
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if ($processedValues->hasValue($this->paramToCompare) !== true) {
            $message = sprintf(
                Messages::ERROR_NO_PREVIOUS_PARAMETER,
                $this->paramToCompare
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        $previousValue = $processedValues->getValue($this->paramToCompare);

        if (!($previousValue instanceof \DateTimeInterface)) {
            $message = sprintf(
                Messages::PREVIOUS_TIME_MUST_BE_DATETIMEINTERFACE,
                $this->paramToCompare
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        if (!($value instanceof \DateTimeInterface)) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE
            );
        }

        $timeOffset = new \DateInterval('PT'  . $this->minutesLater . 'M');

        /** @var \DateTimeImmutable|\DateTime $previousValue */
        $timeToCompare = $previousValue->add($timeOffset);

        if ($value > $timeToCompare) {
            return ValidationResult::valueResult($value);
        }


        $message = sprintf(
            Messages::TIME_MUST_BE_X_MINUTES_AFTER_PARAM_ERROR,
            $value->format(\DateTime::RFC3339),
            $this->minutesLater,
            $this->paramToCompare,
            $previousValue->format(\DateTime::RFC3339)
        );

        return ValidationResult::errorResult($inputStorage, $message);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $message = sprintf(
            Messages::TIME_MUST_BE_X_MINUTES_AFTER_PREVIOUS_VALUE,
            $this->minutesLater,
            $this->paramToCompare
        );

        $paramDescription->setDescription($message);
    }
}
