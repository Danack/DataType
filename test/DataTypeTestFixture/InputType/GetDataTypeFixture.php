<?php

declare(strict_types=1);

namespace DataTypeTestFixture\InputType;

use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\InputType\GetDataType;
use DataTypeTestFixture\Integration\ReviewScore;

class GetDataTypeFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[GetDataType('review', ReviewScore::class)]
        public readonly ReviewScore $review,
    ) {
    }
}
