<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\ExtractRule\GetString;

use DataType\DataType;
use DataType\ProcessRule\MaxLength;
use DataType\SafeAccess;
use DataType\Create\CreateFromVarMap;
use DataType\Create\CreateOrErrorFromVarMap;
use DataType\ExtractRule\GetArrayOfType;
use DataType\InputType;

class ItemParams implements DataType
{
    use SafeAccess;
    use CreateFromVarMap;
    use CreateOrErrorFromVarMap;

    /** @var \DataTypeTest\Integration\ReviewScore[]  */
    private $items;

    /** @var string */
    private $description;

    /**
     * @param \DataTypeTest\Integration\ReviewScore[] $items
     * @param string $description
     */
    public function __construct(array $items, string $description)
    {
        $this->items = $items;
        $this->description = $description;
    }

    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'items',
                new GetArrayOfType(ReviewScore::class)
            ),
            new InputType(
                'description',
                new GetString(),
                new MaxLength(120)
            ),
        ];
    }

    /**
     * @return ReviewScore[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
