<?php

declare(strict_types=1);

/**
 * Same SearchDataType as the opening example, without attributes.
 * Doc extract: Example_datatype_without_attributes
 */

namespace BristolianDatatypeExamples\WithoutAttributes;

use DataType\Create\CreateFromRequest;
use DataType\DataType;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\ExtractRule\GetString;
use DataType\InputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\Trim;

// Example_datatype_without_attributes start
class SearchDataType implements DataType
{
    use CreateFromRequest;

    public function __construct(
        public readonly string $search,
        public readonly int $limit,
    ) {
    }

    /**
     * @return InputType[]
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'search',
                new GetString(),
                new Trim(),
                new MinLength(3),
                new MaxLength(200),
            ),
            new InputType(
                'limit',
                new GetIntOrDefault(10),
            ),
        ];
    }
}
// Example_datatype_without_attributes end
