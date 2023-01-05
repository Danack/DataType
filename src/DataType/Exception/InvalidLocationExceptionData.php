<?php

declare(strict_types = 1);

namespace DataType\Exception;

/**
 * Thrown when someone calls getValue DataStorage object
 */
class InvalidLocationExceptionData extends DataTypeException
{
    /**
     * @var string[]
     */
    private array $location;

    /**
     * @param string[] $location
     * @param string $message
     */
    private function __construct(array $location, string $message)
    {
        $this->location = $location;
        parent::__construct($message);
    }

    /**
     * @param string[] $location
     * @return self
     */
    public static function badArrayDataStorage(array $location): self
    {
        $message = sprintf(
            "Invalid location detected in ArrayDataStorage. This shouldn't happen and is likely a bug in the DataType library. Location was %s",
            implode(", ", $location)
        );

        return new self(
            $location,
            $message
        );
    }

    /**
     * @param string[] $location
     * @return self
     */
    public static function badComplexDataStorage(array $location): self
    {
        $message = sprintf(
            "Invalid location detected in ComplexDataStorage. This shouldn't happen and is likely a bug in the DataType library. Location was %s",
            implode(", ", $location)
        );

        return new self(
            $location,
            $message
        );
    }

    /**
     * @return string[]
     */
    public function getLocation(): array
    {
        return $this->location;
    }
}
