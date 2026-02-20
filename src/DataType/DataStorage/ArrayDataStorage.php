<?php

declare(strict_types = 1);

namespace DataType\DataStorage;

use DataType\Exception\InvalidLocationExceptionData;

/**
 * Implementation of InputStorage that wraps around a simple array.
 *
 * All entries in the hierarchy of data must be either an array or a scalar value.
 * i.e. no objects in it.
 *
 * TODO: Refactor getCurrentValue() and isValueAvailable() so that after $data = $data[$key]
 * the type is narrowed (e.g. assert is_array($data) when more path segments remain) to satisfy
 * PHPStan without ignoring. This is internal-only code so the ignore is acceptable for now.
 */
class ArrayDataStorage implements DataStorage
{
    /**
     * @var array<string|int, mixed>
     */
    private array $data;

    /**
     * @var array<string>
     */
    private array $currentLocation = [];

    /**
     * @param array<string|int, mixed> $data
     */
    protected function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array<string|int, mixed> $data
     */
    public static function fromArray(array $data): DataStorage
    {
        $instance = new self($data);

        return $instance;
    }


    /**
     * @return mixed
     */
    public function getCurrentValue(): mixed
    {
        $data = $this->data;

        foreach ($this->currentLocation as $key) {
            if (array_key_exists($key, $data) !== true) {
                // This would only happen if this was called
                // when the data had been move to a 'wrong' place.
                throw InvalidLocationExceptionData::badArrayDataStorage($this->currentLocation);
            }

            $data = $data[$key];
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function isValueAvailable(): bool
    {
        $data = $this->data;

        foreach ($this->currentLocation as $location) {
            if (array_key_exists($location, $data) === false) {
                return false;
            }

            $data = $data[$location];
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function moveKey(string|int $name): DataStorage
    {
        $clone = clone $this;
        $clone->currentLocation[] = (string)$name;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        if (count($this->currentLocation) === 0) {
            return '/';
        }

        $path = "/" . implode('/', $this->currentLocation);

        return $path;
    }
}
