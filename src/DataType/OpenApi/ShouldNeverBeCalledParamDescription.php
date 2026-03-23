<?php

declare(strict_types=1);

namespace DataType\OpenApi;

use DataType\Exception\OpenApiExceptionData;

/**
 * Used for testing that Rules that shouldn't affect
 * the parameter descriptions.
 * @codeCoverageIgnore
 */
class ShouldNeverBeCalledParamDescription implements ParamDescription
{
    public function setName(string $name): void
    {
        throw new OpenApiExceptionData("setName should not be called.");
    }

    public function setIn(string $in): void
    {
        throw new OpenApiExceptionData("setIn should not be called.");
    }

    public function setDescription(string $description): void
    {
        throw new OpenApiExceptionData("setDescription should not be called.");
    }

    public function getFormat(): ?string
    {
        throw new OpenApiExceptionData("getFormat should not be called.");
    }

    public function setRequired(bool $required): void
    {
        throw new OpenApiExceptionData("setRequired should not be called.");
    }

    public function getRequired(): ?bool
    {
        throw new OpenApiExceptionData("getRequired should not be called.");
    }

    public function setSchema(string $schema): void
    {
        throw new OpenApiExceptionData("setSchema should not be called.");
    }

    public function setType(string $type): void
    {
        throw new OpenApiExceptionData("setType should not be called.");
    }

    public function setFormat(string $format): void
    {
        throw new OpenApiExceptionData("setFormat should not be called.");
    }

    public function setAllowEmptyValue(bool $allowEmptyValue): void
    {
        throw new OpenApiExceptionData("setAllowEmptyValue should not be called.");
    }

    public function getItems(): ItemsObject
    {
        throw new OpenApiExceptionData("getItems should not be called.");
    }

    public function setItems(ItemsObject $itemsObject): void
    {
        throw new OpenApiExceptionData("setItems should not be called.");
    }

    public function setCollectionFormat(string $collectionFormat): void
    {
        throw new OpenApiExceptionData("setCollectionFormat should not be called.");
    }

    public function getCollectionFormat(): ?string
    {
        throw new OpenApiExceptionData("getCollectionFormat should not be called.");
    }

    public function setDefault($default): void
    {
        throw new OpenApiExceptionData("setDefault should not be called.");
    }

    public function setMaximum($maximum): void
    {
        throw new OpenApiExceptionData("setMaximum should not be called.");
    }

    public function setExclusiveMaximum(bool $exclusiveMaximum): void
    {
        throw new OpenApiExceptionData("setExclusiveMaximum should not be called.");
    }

    public function setMinimum($minimum): void
    {
        throw new OpenApiExceptionData("setMinimum should not be called.");
    }

    public function setExclusiveMinimum(bool $exclusiveMinimum): void
    {
        throw new OpenApiExceptionData("setExclusiveMinimum should not be called.");
    }

    public function setMaxLength(int $maxLength): void
    {
        throw new OpenApiExceptionData("setMaxLength should not be called.");
    }

    public function setMinLength(int $minLength): void
    {
        throw new OpenApiExceptionData("setMinLength should not be called.");
    }

    public function setPattern(string $pattern): void
    {
        throw new OpenApiExceptionData("setPattern should not be called.");
    }

    public function setMaxItems(int $maxItems): void
    {
        throw new OpenApiExceptionData("setMaxItems should not be called.");
    }

    public function setMinItems(int $minItems): void
    {
        throw new OpenApiExceptionData("setMinItems should not be called.");
    }

    public function setNullAllowed(bool $allowed): void
    {
        throw new OpenApiExceptionData("setNullAllowed should not be called.");
    }

    public function setUniqueItems(bool $uniqueItems): void
    {
        throw new OpenApiExceptionData("setUniqueItems should not be called.");
    }

    /**
     * @param list<mixed> $enumValues
     * @throws OpenApiExceptionData
     */
    public function setEnum(array $enumValues): void
    {
        throw new OpenApiExceptionData("setEnum should not be called.");
    }

    public function setMultipleOf($multiple): void
    {
        throw new OpenApiExceptionData("setMultipleOf should not be called.");
    }

    public function getDescription(): ?string
    {
        throw new OpenApiExceptionData("getDescription should not be called.");
    }

    /**
     * @return array<int, mixed>|null
     */
    public function getEnum(): ?array
    {
        throw new OpenApiExceptionData("getEnum should not be called.");
    }

    public function getMaxItems(): ?int
    {
        throw new OpenApiExceptionData("getMaxItems should not be called.");
    }

    public function getMinItems(): ?int
    {
        throw new OpenApiExceptionData("getMinItems should not be called.");
    }

    public function getNullAllowed(): ?bool
    {
        throw new OpenApiExceptionData("getNullAllowed should not be called.");
    }

    /**
     * @return int|float|null
     */
    public function getMaximum()
    {
        throw new OpenApiExceptionData("getMaximum should not be called.");
    }

    /**
     * @return int|float|null
     */
    public function getMinimum()
    {
        throw new OpenApiExceptionData("getMinimum should not be called.");
    }

    public function getMaxLength(): ?int
    {
        throw new OpenApiExceptionData("getMaxLength should not be called.");
    }

    public function getMinLength(): ?int
    {
        throw new OpenApiExceptionData("getMinLength should not be called.");
    }

    public function isExclusiveMaximum(): ?bool
    {
        throw new OpenApiExceptionData("isExclusiveMaximum should not be called.");
    }

    public function isExclusiveMinimum(): ?bool
    {
        throw new OpenApiExceptionData("isExclusiveMinimum should not be called.");
    }

    public function getExclusiveMinimum(): ?bool
    {
        throw new OpenApiExceptionData("getExclusiveMinimum should not be called.");
    }

    public function getExclusiveMaximum(): ?bool
    {
        throw new OpenApiExceptionData("getExclusiveMaximum should not be called.");
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        throw new OpenApiExceptionData("getDefault should not be called.");
    }

    /**
     * @return array<int, mixed>|null
     */
    public function getEnumValues(): ?array
    {
        throw new OpenApiExceptionData("getEnumValues should not be called.");
    }

    public function getType(): ?string
    {
        throw new OpenApiExceptionData("getType should not be called.");
    }
}
