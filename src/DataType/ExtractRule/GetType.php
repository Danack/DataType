<?php

declare(strict_types=1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;
use function DataType\createObjectFromProcessedValues;
use function DataType\getInputTypeListForClass;
use function DataType\processInputTypesFromStorage;

/**
 * Extracts a DataType value.
 */
class GetType implements ExtractRule
{
    /** @var class-string */
    private string $className;

    /** @var \DataType\InputType[] */
    private array $inputTypes;

    /**
     * @param class-string $className
     * @param \DataType\InputType[] $dataTypeList
     */
    public function __construct(string $className, $dataTypeList)
    {
        $this->className = $className;
        $this->inputTypes = $dataTypeList;
    }

    /**
     * @param class-string $classname
     */
    public static function fromClass(string $classname): self
    {
        return new self(
            $classname,
            getInputTypeListForClass($classname)
        );
    }

    /**
     * @param class-string $className
     * @param \DataType\InputType[] $inputTypes
     */
    public static function fromClassAndInputTypes(string $className, $inputTypes): self
    {
        return new self(
            $className,
            $inputTypes
        );
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $newProcessedValues = new ProcessedValues();
        $validationProblems = processInputTypesFromStorage(
            $this->inputTypes,
            $newProcessedValues,
            $dataStorage
        );

        if (count($validationProblems) !== 0) {
            return ValidationResult::fromValidationProblems($validationProblems);
        }

        $item = createObjectFromProcessedValues($this->className, $newProcessedValues);

        return ValidationResult::valueResult($item);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - how to implement this.
        $paramDescription->setRequired(true);
    }
}
