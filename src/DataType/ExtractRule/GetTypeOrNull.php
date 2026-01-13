<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Extracts a DataType value or null.
 *
 * This pattern is probably 'not best practice' for an API, and just not including the key/value
 * is possibly better to indicate a lack of an optional value.
 */
class GetTypeOrNull implements ExtractRule
{
    private GetType $getType;

    private function __construct(GetType $getType)
    {
        $this->getType = $getType;
    }

    /**
     * @param class-string $classname
     */
    public static function fromClass(string $classname): self
    {
        $instance = new self(GetType::fromClass($classname));

        return $instance;
    }

    /**
     * @param class-string $className
     * @param \DataType\InputType[] $inputTypes
     */
    public static function fromClassAndRules(string $className, $inputTypes): self
    {
        $getType = GetType::fromClassAndInputTypes(
            $className, $inputTypes
        );

        return new self($getType);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $value = $dataStorage->getCurrentValue();

        if (is_null($value) === true) {
            return ValidationResult::valueResult($value);
        }

        return $this->getType->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setRequired(true);
        $paramDescription->setNullAllowed(true);
    }
}
