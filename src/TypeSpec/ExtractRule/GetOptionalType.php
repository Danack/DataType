<?php

declare(strict_types=1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Class GetOptionalInt
 *
 * If a parameter is not set, then the value is null, otherwise
 * it must a valid set of data for that type
 *
 */
class GetOptionalType implements ExtractPropertyRule
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
     * @param \TypeSpec\DataType[] $inputParameterList
     */
    public static function fromClassAndRules(string $className, $inputParameterList): self
    {
        $getType = GetType::fromClassAndRules(
            $className, $inputParameterList
        );

        return new self($getType);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        return $this->getType->process(
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(false);
    }
}
