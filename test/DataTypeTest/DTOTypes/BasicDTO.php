<?php

declare(strict_types = 1);

namespace DataTypeTest\DTOTypes;

use DataType\DataType;
use DataTypeTest\InputType\KnownColors;
use DataTypeTest\InputType\Quantity;
use DataType\GetInputTypesFromAttributes;

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
