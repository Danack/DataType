<?php

declare(strict_types = 1);

namespace DataType\ExtractRule;

use DataType\DataStorage\DataStorage;
use DataType\Messages;
use DataType\OpenApi\ParamDescription;
use DataType\ProcessedValues;
use DataType\ValidationResult;
use function DataType\createArrayOfTypeFromInputStorage;
use function DataType\getDataTypeListForClass;

/**
 * Extracts an array of a DataType.
 */
class GetArrayOfType implements ExtractRule
{
    /** @var class-string */
    private string $className;

    /** @var \DataType\InputType[] */
    private array $inputTypes;

    private GetType $typeExtractor;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        $this->inputTypes = getDataTypeListForClass($this->className);

        $this->typeExtractor = GetType::fromClassAndInputTypes(
            $this->className,
            $this->inputTypes
        );
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {

        // Check it is set
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult(
                $dataStorage,
                Messages::ERROR_MESSAGE_NOT_SET_VARIANT_1
            );
        }

        return createArrayOfTypeFromInputStorage(
            $dataStorage,
            $this->typeExtractor
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
