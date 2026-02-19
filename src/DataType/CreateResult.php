<?php

declare(strict_types=1);

namespace DataType;

/**
 * Result of attempting to create a DataType instance from input.
 * Preserves the created type and provides helper methods instead of a tuple.
 *
 * @experimental The CreateResult class is an experiment in making the library easier to use. It is not guaranteed to be in future versions. If you like it, please let Danack know.
 *
 * @template T of object
 */
final class CreateResult
{
    /**
     * @param T|null $value The created instance when valid, null when invalid.
     * @param \DataType\ValidationProblem[] $validationProblems Validation errors when invalid.
     */
    private function __construct(
        private readonly object|null $value,
        private readonly array $validationProblems
    ) {
    }

    /**
     * @template TValue of object
     * @param TValue $value
     * @return self<TValue>
     */
    public static function success(object $value): self
    {
        return new self($value, []);
    }

    /**
     * @param \DataType\ValidationProblem[] $validationProblems
     * @return self<object>
     */
    public static function failure(array $validationProblems): self
    {
        return new self(null, $validationProblems);
    }

    /**
     * Whether input was valid and the instance was created.
     */
    public function isValid(): bool
    {
        return count($this->validationProblems) === 0;
    }

    /**
     * The created instance when valid, null when invalid.
     *
     * @return T|null
     */
    public function getValue(): object|null
    {
        return $this->value;
    }

    /**
     * Validation errors when invalid, empty array when valid.
     *
     * @return \DataType\ValidationProblem[]
     */
    public function getErrors(): array
    {
        return $this->validationProblems;
    }
}
