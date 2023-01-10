<?php

declare(strict_types = 1);

namespace DataType\ProcessRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;
use DataType\Value\OrderElement;
use DataType\Value\Ordering;
use function DataType\array_value_exists;
use function DataType\normalise_order_parameter;

/**
 * Supports a parameter to specify ordering of results
 * For example "+name,-date" would be equivalent to ordering
 * by name ascending, then date descending.
 *
 * Final value is an array, each element of which contains an array
 * with the string, and ordering e.g.
 * ```php
 * [
 *     ['name', Ordering::ASC] // Ordering::ASC is the string 'asc'
 *     ['date', Ordering::DESC] // Ordering::DESC is the string 'desc'
 * ]
 * ```
 */
class Order implements ProcessRule
{
    use CheckString;

    /** @var string[] */
    private array $knownOrderNames;

    /**
     * OrderValidator constructor.
     * @param string[] $knownOrderNames
     */
    public function __construct(array $knownOrderNames)
    {
        $this->knownOrderNames = $knownOrderNames;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        $parts = explode(',', $value);
        $orderElements = [];

        foreach ($parts as $part) {
            list($partName, $partOrder) = normalise_order_parameter($part);
            if (array_value_exists($this->knownOrderNames, $partName) !== true) {
                $message = sprintf(
                    Messages::ORDER_VALUE_UNKNOWN,
                    $partName,
                    implode(', ', $this->knownOrderNames)
                );

                return ValidationResult::errorResult($inputStorage, $message);
            }
            $orderElements[] = new OrderElement($partName, $partOrder);
        }

        return ValidationResult::valueResult(new Ordering($orderElements));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_ARRAY);
        $paramDescription->setCollectionFormat(ParamDescription::COLLECTION_CSV);
    }
}
