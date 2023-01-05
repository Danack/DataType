<?php

declare(strict_types = 1);

namespace DataType;

use DataType\Exception\LogicExceptionData;

/**
 * A class to stores the processed parameters, so that they can be accessed by subsequent rules.
 *
 * This is useful for when you want to have a rule that one parameter must be a
 * duplicate of another parameter. e.g. email double-entry
 */
class ProcessedValues
{
    /** @var ProcessedValue[]  */
    private array $processedValues = [];

    /**
     * @param ProcessedValue[] $processedValues
     * @return self
     * @throws LogicExceptionData
     */
    public static function fromArray(array $processedValues): self
    {
        foreach ($processedValues as $processedValue) {
            /** @psalm-suppress DocblockTypeContradiction */
            if (!($processedValue instanceof ProcessedValue)) {
                throw LogicExceptionData::onlyProcessedValues();
            }
        }

        $instance = new self();
        $instance->processedValues = $processedValues;

        return $instance;
    }

    /**
     * TODO - is this required?
     * Gets the currently processed values.
     * @return array<int|string, mixed>
     */
    public function getAllValues()
    {
        $values = [];
        foreach ($this->processedValues as $processedValue) {
            $values[$processedValue->getInputType()->getName()] = $processedValue->getValue();
        }

        return $values;
    }

    public function getCount(): int
    {
        return count($this->processedValues);
    }

    /**
     * @return ProcessedValue[]
     */
    public function getProcessedValues(): array
    {
        return $this->processedValues;
    }

    /**
     * @param string|int $name
     */
    public function hasValue($name): bool
    {
        foreach ($this->processedValues as $processedValue) {
            if ($name === $processedValue->getInputType()->getName()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getValue(string $name): mixed
    {
        foreach ($this->processedValues as $processedValue) {
            if ($name === $processedValue->getInputType()->getName()) {
                return $processedValue->getValue();
            }
        }
        throw LogicExceptionData::missingValue($name);
    }


    /**
     * @param InputType $inputType
     * @param mixed $value
     */
    public function setValue(InputType $inputType, mixed $value): void
    {
        $this->processedValues[] = new ProcessedValue($inputType, $value);
    }

    /**
     * Gets the value to inject into an object for a particular
     * property.
     *
     * @param string $name The name of the property to find the value for
     * @return array{0:null, 1:false}|array{0:mixed, 1:true}
     */
    public function getValueForTargetProperty(string $name): array
    {
        foreach ($this->processedValues as $processedValue) {
            if ($name === $processedValue->getInputType()->getTargetParameterName()) {
                return [$processedValue->getValue(), true];
            }
        }

        return [null, false];
    }
}
