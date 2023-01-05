<?php

declare(strict_types = 1);

namespace DataTypeExample\DTOTypes;

use DataTypeExample\InputTypes\KnownColors;
use DataTypeExample\InputTypes\Quantity;
use DataType\GetInputTypesFromAttributes;
use DataType\DataType;

class TestDTO implements DataType
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