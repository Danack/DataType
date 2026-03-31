<?php

declare(strict_types = 1);

namespace DataTypeTestFixture\DTOTypes;

use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataTypeTestFixture\InputType\KnownColors;
use DataTypeTestFixture\InputType\Quantity;

class BasicDTO implements DataType
{
    use GetInputTypesFromAttributes;

    public function __construct(
        #[KnownColors('color')]
        public string $color,
        #[Quantity('quantity')]
        public int $quantity,
    ) {
    }
}
