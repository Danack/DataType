<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\OpenApi\ParamDescription;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataType\ValidationResult;

/**
 * Tri-state nested DataType: absent, explicit null, or decoded object (same shape as {@see GetType} when present).
 */
class GetOptionalNullableType implements ExtractRule
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
        return new self(GetType::fromClass($classname));
    }

    /**
     * @param class-string $className
     * @param \DataType\InputType[] $inputTypes
     */
    public static function fromClassAndRules(string $className, array $inputTypes): self
    {
        return new self(GetType::fromClassAndInputTypes($className, $inputTypes));
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::finalValueResult(Absent::instance());
        }

        if ($dataStorage->getCurrentValue() === null) {
            return ValidationResult::finalValueResult(PresentNull::instance());
        }

        return $this->getType->process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setRequired(false);
        $paramDescription->setNullAllowed(true);
    }
}
